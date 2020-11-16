<?php

namespace Abdrzakoxa\EloquentFilter\Filters;

use Abdrzakoxa\EloquentFilter\Contracts\ForceApply;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class LimitFilter
 * @package Abdrzakoxa\EloquentFilter\Filters
 */
class LimitFilter implements ForceApply
{
    /**
     * @var int
     */
    protected $maxLimit = 100;

    /**
     * @var int
     */
    protected $defaultLimit = 10;

    /**
     * LimitFilter constructor.
     * @param  int|null  $maxLimit
     * @param  int|null  $defaultLimit
     */
    public function __construct(?int $maxLimit = null, ?int $defaultLimit = null)
    {
        if (! is_null($maxLimit)) {
            $this->maxLimit = $maxLimit;
        }
        if (! is_null($defaultLimit)) {
            $this->defaultLimit = $defaultLimit;
        }
    }

    /**
     * Apply the filter after validation passes & sanitize
     * @param int $value
     * @param  Builder  $builder
     */
    public function handle(int $value, Builder $builder): void
    {
        $builder->limit($value);
    }

    /**
     * @param mixed $value
     * @return int
     */
    public function sanitize($value): int
    {
        if (is_string($value)) {
            $value = (int) $value;
        }

        if (! is_int($value) || $value <= 0 || $value > $this->maxLimit) {
            $value = $this->defaultLimit;
        }

        return $value;
    }
}
