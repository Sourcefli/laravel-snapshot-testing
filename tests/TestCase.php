<?php

namespace Sourcefli\SnapshotTesting\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Sourcefli\SnapshotTesting\SnapshotTestingServiceProvider;
use Spatie\LaravelRay\RayServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
	{
        return [
			RayServiceProvider::class,
            SnapshotTestingServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
