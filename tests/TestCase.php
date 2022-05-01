<?php

namespace Sourcefli\SnapshotTesting\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Sourcefli\SnapshotTesting\SnapshotTestingServiceProvider;
use Spatie\LaravelRay\RayServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
	{
        return [
			RayServiceProvider::class,
            SnapshotTestingServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
		$app['config']->set('database.default', 'testing');
    }
}
