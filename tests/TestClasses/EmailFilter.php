<?php

namespace Abdrzakoxa\EloquentFilter\Test\TestClasses;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class EmailFilter
{
    /**
     * Apply the filter after validation passes
     * @param $value
     * @param  Builder  $builder
     */
    public function handle($value, Builder $builder): void
    {
        $builder->where('email', $value);
    }

    /**
     * @param $value
     * @return bool|string|array
     */
    public function validate($value)
    {
        return is_string($value);
    }

    /**
     * @param $value
     * @return string
     */
    public function sanitize($value): string
    {
        if (! is_string($value)) {
            $value = 'no-reply';
        }

        if (! Str::contains('@', $value)) {
            return "$value@default-company.com";
        }

        return $value;
    }
}
