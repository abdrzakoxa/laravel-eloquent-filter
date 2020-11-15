<?php

namespace Abdrzakoxa\EloquentFilter\Test\TestClasses;

use Abdrzakoxa\EloquentFilter\Traits\Filterable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * @package Abdrzakoxa\EloquentFilter\Test\TestClasses
 * @property-read int $id
 * @property-read string $name
 * @property-read string $email
 * @property-read string|null $phone
 * @property-read Carbon|string $created_at
 * @property-read Carbon|string $updated_at
 */
class User extends Model
{
    use Filterable;

    protected $fillable = [
        'name', 'email', 'phone'
    ];
}
