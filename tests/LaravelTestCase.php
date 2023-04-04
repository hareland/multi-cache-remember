<?php

namespace Hareland\MultiCacheRemember\Tests;

use Hareland\MultiCacheRemember\MultiCacheServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class LaravelTestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
//
    }

    protected function getPackageProviders($app)
    {
        return [
            MultiCacheServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
//        $app['config']->set('database.default', 'testbench');
//        $app['config']->set('database.connections.testbench', [
//            'driver' => 'sqlite',
//            'database' => ':memory:',
//            'prefix' => '',
//        ]);
    }
}