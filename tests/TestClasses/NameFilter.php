<?php

namespace Abdrzakoxa\EloquentFilter\Test\TestClasses;

use Illuminate\Database\Eloquent\Builder;

class NameFilter
{
    /**
     * Apply the filter after validation passes
     * @param $value
     * @param  Builder  $builder
     */
    public function handle($value, Builder $builder): void
    {
        $builder->where('name', $value);
    }

    /**
     * @return bool|string|array
     */
    public function validate()
    {
        return 'string|max:150';
    }
}
