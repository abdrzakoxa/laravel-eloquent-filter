<?php

namespace Abdrzakoxa\EloquentFilter\Test;

use Abdrzakoxa\EloquentFilter\Test\TestClasses\User;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Testbench\Concerns\WithLaravelMigrations;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use WithLaravelMigrations;

    /**
     * @var Generator
     */
    protected $faker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
        $this->migrateDb();
    }

    protected function migrateDb(): void
    {
        $landLordMigrationsPath = realpath(__DIR__ . '/migrations');
        $landLordMigrationsPath = str_replace('\\', '/', $landLordMigrationsPath);

        $this->artisan("migrate --database=sqlite --path={$landLordMigrationsPath} --realpath")
            ->assertExitCode(0);
    }

    protected function defineEnvironment($app)
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ],
        ]);
    }

    /**
     * @param  string  $name
     * @param  string  $email
     * @param  string|null  $phone
     * @return Builder|Model|User
     */
    protected function createUser(string $name, string $email, ?string $phone)
    {
        return User::query()->create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ]);
    }

    /**
     * @param $times
     * @param  callable  $callback
     */
    public function doitfor($times, callable $callback): void
    {
        for ($x = 0; $x < $times; $x++) {
            $callback();
        }
    }
}
