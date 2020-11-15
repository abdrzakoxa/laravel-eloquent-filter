<?php

namespace Abdrzakoxa\EloquentFilter\Filters;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class BetweenFilter
 * @package Abdrzakoxa\EloquentFilter\Filters
 */
class BetweenFilter
{
    /**
     * the default name of filter
     * @var string|array
     */
    public static $name = 'between';

    /**
     * @var string
     */
    protected $defaultColumn = 'created_at';

    /**
     * Apply the filter after validation passes
     * @param array $value
     * @param  Builder  $builder
     * @param $column
     */
    public function handle(array $value, Builder $builder, ?string $column = null): void
    {
        $builder->whereBetween($column ?: $this->defaultColumn, $value);
    }

    /**
     * @return bool|string|array
     */
    public function validate()
    {
        return [
            'value' => 'array|size:2',
            'value.0' => 'date',
            'value.1' => 'date|after:value.0'
        ];
    }
}
