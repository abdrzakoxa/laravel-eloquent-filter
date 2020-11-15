<?php

namespace Abdrzakoxa\EloquentFilter\Filters;

use Abdrzakoxa\EloquentFilter\Contracts\ForceApply;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class SortingFilter
 * @package Abdrzakoxa\EloquentFilter\Filters
 */
class SortingFilter implements ForceApply
{
    /**
     * @var string
     */
    protected $defaultSorting = 'desc';

    /**
     * @var string
     */
    protected $defaultColumn = 'created_at';

    /**
     * Apply the filter after validation passes & sanitize
     * @param string $value
     * @param  Builder  $builder
     * @param $column
     */
    public function handle(string $value, Builder $builder, ?string $column = null): void
    {
        $builder->orderBy($column ?: $this->defaultColumn, $value);
    }

    /**
     * @param mixed $value
     * @return bool|string|array
     */
    public function sanitize($value)
    {
        return is_string($value) && in_array($value, ['desc', 'asc']) ? $value : $this->defaultSorting;
    }
}
