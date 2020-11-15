<?php

namespace Abdrzakoxa\EloquentFilter\Test\Feature;

use Abdrzakoxa\EloquentFilter\Filter;
use Abdrzakoxa\EloquentFilter\Filters\BetweenFilter;
use Abdrzakoxa\EloquentFilter\Filters\LimitFilter;
use Abdrzakoxa\EloquentFilter\Filters\SortingFilter;
use Abdrzakoxa\EloquentFilter\Test\TestCase;
use Abdrzakoxa\EloquentFilter\Test\TestClasses\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class FiltersTest extends TestCase
{
    public function test_between_filter(): void
    {
        $this->createUser($this->faker->unique()->name, $this->faker->email, $this->faker->phoneNumber);
        $this->travelTo(now()->addMonth());
        $targetModel = $this->createUser($this->faker->unique()->name, $this->faker->email, null);

        $searchedData = [
            'created_between' => [
                $targetModel->created_at->clone()->subDay(),
                $targetModel->created_at->clone()->addDay()
            ],
        ];

        $filter = (new Filter(User::class, $searchedData, [
            BetweenFilter::class . ':created_at' => 'created_between',
        ]))->filter()->get();

        self::assertEquals(1, $filter->count());
        self::assertTrue($filter->first()->is($targetModel));
    }

    public function test_sorting_filter(): void
    {
        $firstUser = $this->createUser($this->faker->unique()->name, $this->faker->email, $this->faker->phoneNumber);
        $this->travelTo(now()->addDay());
        $lastUser = $this->createUser($this->faker->unique()->name, $this->faker->email, null);

        //// test desc

        $searchedData = [
            'sorting' => 'desc'
        ];
        $filteredData = (new Filter(User::class, $searchedData, [
            SortingFilter::class,
        ]))->filter()->get();

        self::assertTrue($filteredData->first()->is($lastUser));

        //// test asc

        $searchedData = [
            'sorting' => 'asc'
        ];
        $filteredData = (new Filter(User::class, $searchedData, [
            SortingFilter::class,
        ]))->filter()->get();

        self::assertTrue($filteredData->first()->is($firstUser));
    }

    public function test_limit_filter(): void
    {
        $this->doitfor(11, function () {
            return $this->createUser($this->faker->unique()->name, $this->faker->email, $this->faker->phoneNumber);
        });

        $filteredData = $this->executeLimitFilter(10, 10);
        self::assertEquals(10, $filteredData->count());

        $filteredData = $this->executeLimitFilter(10, 5, 2);
        self::assertEquals(2, $filteredData->count());

        $filteredData = $this->executeLimitFilter(null, 100, 6);
        self::assertEquals(6, $filteredData->count());
    }

    /**
     * @param  mixed  $limit
     * @param int|null $maxLimit
     * @param int|null $defaultLimit
     * @return Builder[]|Collection
     */
    protected function executeLimitFilter($limit, ?int $maxLimit = null, ?int $defaultLimit = null)
    {
        $searchedData = [
            'limit' => $limit
        ];
        if (! is_null($maxLimit) || ! is_null($defaultLimit)) {
            $parameters = rtrim(":$maxLimit,$defaultLimit", ',');
        }
        return (new Filter(User::class, $searchedData, [
            LimitFilter::class . ($parameters ?? ''),
        ]))->filter()->get();
    }
}
