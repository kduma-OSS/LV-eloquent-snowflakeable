<?php

namespace KDuma\Eloquent\Internal;


use Godruoyi\Snowflake\Snowflake as BaseSnowflake;
use Godruoyi\Snowflake\SnowflakeException;

class Snowflake extends BaseSnowflake
{
    /**
     * @throws SnowflakeException
     */
    public function idFor(\DateTime $timestamp): string
    {
        $currentTime = (int) $timestamp->format('Uv');

        $missTime = $currentTime - $this->getStartTimeStamp();
        if ($missTime < 0) {
            throw new SnowflakeException('The start time cannot be greater than the current time');
        }

        while (($sequence = $this->callResolver($currentTime)) > (-1 ^ (-1 << self::MAX_SEQUENCE_LENGTH))) {
            usleep(1);
            $currentTime++;
        }

        $workerLeftMoveLength = self::MAX_SEQUENCE_LENGTH;
        $datacenterLeftMoveLength = self::MAX_WORKID_LENGTH + $workerLeftMoveLength;
        $timestampLeftMoveLength = self::MAX_DATACENTER_LENGTH + $datacenterLeftMoveLength;

        return (string) ((($currentTime - $this->getStartTimeStamp()) << $timestampLeftMoveLength)
            | ($this->datacenter << $datacenterLeftMoveLength)
            | ($this->workerId << $workerLeftMoveLength)
            | ($sequence));
    }
}
