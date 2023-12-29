<?php

namespace KDuma\Eloquent;

use Godruoyi\Snowflake\IdGenerator;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @method static self whereSnowflakeId(string $snowflake)
 * @property-read ParsedSnowflake $snowflake
 */
trait Snowflakeable
{
    /**
     * Boot the trait.
     */
    protected static function bootSnowflakeable(): void
    {
        static::creating(function (Model $model) {
            $model->generateSnowflakeOnCreateOrUpdate();
        });

        static::updating(function (Model $model) {
            $model->generateSnowflakeOnCreateOrUpdate();
        });
    }

    /**
     * Gets first model by snowflake
     */
    public static function bySnowflake(string $snowflake): self
    {
        return static::whereSnowflakeId($snowflake)->first();
    }

    public function regenerateSnowflake(\DateTime $for = null, bool $check_for_snowflake_duplicates = null): void
    {
        $this->{$this->getSnowflakeField()} = $this->snowflakeGenerate($for, $check_for_snowflake_duplicates);
    }

    /**
     * Get the Snowflake ID field name associated with the model.
     */
    public function getSnowflakeField(): string
    {
        return $this->snowflake_field ?? 'sfid';
    }

    /**
     * @param $query
     * @param $snowflake
     * @return mixed
     */
    public function scopeWhereSnowflakeId($query, $snowflake): mixed
    {
        return $query->where($this->getTable().'.'.$this->getSnowflakeField(), $snowflake);
    }


    protected function snowflakeGenerate(\DateTime $for = null, bool $check_for_snowflake_duplicates = null): string
    {
        if($for === null) {
            $snowflake = (int) app(IdGenerator::class)->id();
        } else {
            $snowflake = (int) app(IdGenerator::class)->idFor($for);
        }

        if (
            $check_for_snowflake_duplicates === false
            || $check_for_snowflake_duplicates === null & (!isset($this->check_for_snowflake_duplicates) || !$this->check_for_snowflake_duplicates)
        ) {
            return $snowflake;
        }

        $rowCount = DB::table($this->getTable())->where($this->getSnowflakeField(), $snowflake)->count();

        return $rowCount > 0 ? $this->snowflakeGenerate($for, $check_for_snowflake_duplicates) : $snowflake;
    }

    protected function generateSnowflakeOnCreateOrUpdate(): void
    {
        if($this->{$this->getSnowflakeField()} === null) {
            $this->regenerateSnowflake();
        }
    }

    protected function snowflake(): Attribute
    {
        return Attribute::make(get: function () {
            $id = $this->{$this->getSnowflakeField()};

            $generator = app(IdGenerator::class);

            $parsed = $generator->parseId($id, true);
            $parsed['timestamp'] = $generator->toMicrotime($parsed['timestamp']);

            return new ParsedSnowflake(
                id: (int) $id,
                timestamp: $parsed['timestamp'] ?? 0,
                datacenter: $parsed['datacenter'] ?? 0,
                worker: $parsed['workerid'] ?? $parsed['machineid'] ?? 0,
                sequence: $parsed['sequence'] ?? 0,
            );
        });
    }
}
