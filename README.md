<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://user-images.githubusercontent.com/44305005/100349327-8f6aee80-2fe8-11eb-83fe-11d78e412616.PNG"></a></p>

# Laravel Eloquent Filter
<div>
<a href="https://github.com/abdrzakoxa/laravel-eloquent-filter/actions"><img src="https://img.shields.io/github/workflow/status/abdrzakoxa/laravel-eloquent-filter/run-tests?label=tests" alt="Tests"></a>
<a href="https://packagist.org/packages/abdrzakoxa/laravel-eloquent-filter"><img src="https://img.shields.io/packagist/dt/abdrzakoxa/laravel-eloquent-filter" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/abdrzakoxa/laravel-eloquent-filter"><img src="https://img.shields.io/packagist/v/abdrzakoxa/laravel-eloquent-filter" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/abdrzakoxa/laravel-eloquent-filter"><img src="https://img.shields.io/packagist/l/abdrzakoxa/laravel-eloquent-filter" alt="License"></a>
</div>
Scalable & secure way to filter laravel model

## Introduction
Lets say we want to return a list of users filtered by multiple parameters. When we navigate to:

`/users?name=avf&roles[]=admin&roles[]=manager&roles[]=client&limit=10`

`$request->all()` will return:

```php
['name' => 'avf', 'roles' => ['admin', 'manager', 'client'], 'limit' => '10']
```

To filter by all those parameters we would need to do something like:

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        $query->when($request->has('name'), function (Builder $builder) use ($request) {
            $builder->where('name', 'LIKE', '%'.$request->input('name').'%');
        });

        if (auth()->user()->isAdmin()) {
            $allowedRoles = ['admin', 'manager', 'client'];
        } elseif (auth()->user()->isManager()) {
            $allowedRoles = ['client'];
        } else {
            $allowedRoles = [];
        }
        $roles = [];
        foreach ((array) $request->input('roles') as $role) {
            if (in_array($role, $allowedRoles, true)) {
                $roles[] = $role;
            }
        }
        $query->whereHas('roles', function ($q) use ($roles) {
            return $q->whereIn('name', $roles);
        });
        if ($request->has('limit') && is_numeric($request->input('limit')) && $request->input('limit') < 100) {
            $limit = (int) $request->input('limit');
        } else {
            $limit = 10;
        }
        $query->limit($limit);

        return $query->get();
    }
}
```
To filter that same input With Eloquent Filters:
```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return User::filter($request->all())->get();
    }
}
```

## Installation
```
composer require abdrzakoxa/laravel-eloquent-filter
```

## Usage
### Create your custom filter
create `app/EloquentFilters/NameFilter.php` file
```php
<?php

namespace App\EloquentFilters;

use Illuminate\Database\Eloquent\Builder;

class NameFilter
{
    /**
     * Apply the filter after validation passes & sanitize
     * @param string $value
     * @param  Builder  $builder
     */
    public function handle(string $value, Builder $builder): void
    {
        $builder->where('name', $value);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function sanitize($value)
    {
        return is_string($value) ? $value : '';
    }

    /**
     * @param mixed $value
     * @return bool|string|array
     */
    public function validate($value)
    {
        return strlen($value) > 5 && strlen($value) < 100;
    }
}
```
Also, you can use only handle method
#### Apply NameFilter to a model
```php
<?php

namespace App\Models;

use App\EloquentFilters\NameFilter;
use Abdrzakoxa\EloquentFilter\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Filterable;

    protected $filters = [
        NameFilter::class,
    ];
}
```
## Available Filters 
The following filters are available out of the box:
### BetweenFilter
#### usage:
`BetweenFilter` is an available filter to filter between two dates

`/users?approved_between[]=2020-10-03&approved_between[]=2020-11-03&created_between[]=2020-09-01&created_between[]=2020-12-01`

```php
// ...
use Abdrzakoxa\EloquentFilter\Traits\Filterable;
use Abdrzakoxa\EloquentFilter\Filters\BetweenFilter;

class User extends Model
{
    use Filterable;

    protected $filters = [
        BetweenFilter::class . ':approved_at' => 'approved_between',
        BetweenFilter::class => 'created_between',
    ];
}
```
```php
User::filter($request->all())->get();
```
### LimitFilter
#### usage:
`LimitFilter` is an available filter to limit the final result

`/users?limit=10`

```php
// ...
use Abdrzakoxa\EloquentFilter\Traits\Filterable;
use Abdrzakoxa\EloquentFilter\Filters\LimitFilter;

class User extends Model
{
    use Filterable;

    protected $filters = [
        LimitFilter::class
    ];
}
```
```php
User::filter($request->all())->get();
```
### SortingFilter
#### usage:
`LimitFilter` is an available filter to sort the final result

`/users?sorting=asc`

```php
// ...
use Abdrzakoxa\EloquentFilter\Traits\Filterable;
use Abdrzakoxa\EloquentFilter\Filters\SortingFilter;

class User extends Model
{
    use Filterable;

    protected $filters = [
        SortingFilter::class . ':approved_at' // sorting by approved_at column
    ];
}
```
```php
User::filter($request->all())->get();
```

## Contributing
Any contributions welcome!
