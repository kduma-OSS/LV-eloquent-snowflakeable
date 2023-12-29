<?php

namespace KDuma\Eloquent;

use Illuminate\Support\Carbon;

class ParsedSnowflake
{
    public function __construct(
        readonly public int $id,
        readonly public int $timestamp,
        readonly public int $datacenter,
        readonly public int $worker,
        readonly public int $sequence,
    ) { }

    public function getDateTime(): Carbon
    {
        return new Carbon('@'.$this->timestamp/1000);
    }
}
