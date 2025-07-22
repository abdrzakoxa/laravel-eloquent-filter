<?php

namespace Abdrzakoxa\EloquentFilter\Test\Feature;

use Abdrzakoxa\EloquentFilter\Filter;
use Abdrzakoxa\EloquentFilter\Test\TestCase;
use Abdrzakoxa\EloquentFilter\Test\TestClasses\EmailFilter;
use Abdrzakoxa\EloquentFilter\Test\TestClasses\NameFilter;
use Abdrzakoxa\EloquentFilter\Test\TestClasses\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Arr;

class CoreTest extends TestCase
{
    public function test_can_filter(): void
    {
        $targetModel = $this->createUser($this->faker->unique()->name, $this->faker->email, null);
        $this->createUser($this->faker->unique()->name, $this->faker->email, $this->faker->phoneNumber);

        $searchedData = [
            'name' => $targetModel->name,
        ];

        $filter = User::filter($searchedData, [
            NameFilter::class,
        ])->get();

        self::assertEquals(1, $filter->count());
        self::assertTrue($filter->first()->is($targetModel));
    }

    public function test_can_validate_before_filter(): void
    {
        $this->createUser($this->faker->unique()->name, $this->faker->email, null);
        $this->createUser($this->faker->unique()->name, $this->faker->email, $this->faker->phoneNumber);

        $searchedData = [
            'name' => ['test'],
        ];

        $filter = (new Filter(User::class, $searchedData, [
            NameFilter::class,
        ]))->filter()->get();

        self::assertEquals(2, $filter->count());
    }

    public function test_can_use_custom_name(): void
    {
        $targetModel = $this->createUser($this->faker->unique()->name, $this->faker->email, null);
        $this->createUser($this->faker->unique()->name, $this->faker->email, $this->faker->phoneNumber);

        $searchedData = [
            'custom_name' => $targetModel->name,
        ];

        $filteredData = (new Filter(User::class, $searchedData, [
            NameFilter::class => 'custom_name',
        ]))->filter()->get();

        self::assertEquals(1, $filteredData->count());
        self::assertTrue($filteredData->first()->is($targetModel));
    }

    public function test_can_sanitize_before_filter(): void
    {
        $targetModel = $this->createUser($this->faker->name, ($username = $this->faker->unique()->userName) . '@default-company.com', $this->faker->phoneNumber);
        $this->createUser($this->faker->name, $this->faker->email, $this->faker->phoneNumber);

        $filteredData = (new Filter(User::class, ['email' => $username,], [
            EmailFilter::class,
        ]))->filter()->get();

        self::assertEquals(1, $filteredData->count());
        self::assertTrue($filteredData->first()->is($targetModel));
    }

    public function test_can_paginate(): void
    {
        /** @var Paginator $paginate */
        $paginate = User::filter($data = ['name' => $this->faker->name])->simplePaginateFilter();
        parse_str(parse_url($paginate->url(1), PHP_URL_QUERY), $query);
        self::assertEquals(Arr::except($query, 'page'), $data);

        /** @var LengthAwarePaginator $paginate */
        $paginate = User::filter($data = ['name' => $this->faker->name])->paginateFilter();
        parse_str(parse_url($paginate->url(1), PHP_URL_QUERY), $query);
        self::assertEquals(Arr::except($query, 'page'), $data);
    }
}
