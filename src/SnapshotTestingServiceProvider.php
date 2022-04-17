<?php

namespace Sourcefli\SnapshotTesting;

use Illuminate\Support\Arr;
use Sourcefli\SnapshotTesting\Snapshots\Connection as SnapshotConnection;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SnapshotTestingServiceProvider extends PackageServiceProvider
{
	public function configurePackage(Package $package): void
	{
		$package
			->name('laravel-snapshot-testing')
			->hasConfigFile('snapshot-testing');
	}

	public function packageRegistered()
	{
		config(['filesystems.disks.snapshot-testing' => config('snapshot-testing.disk.snapshot-testing')]);

		$this->app->singleton(SnapshotTesting::class, fn ($app) => new SnapshotTesting($app['config']));
		$this->app->singleton(SnapshotConnection::class);
	}

	public function packageBooted()
	{

	}
}
