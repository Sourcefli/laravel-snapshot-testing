<?php

namespace Sourcefli\SnapshotTesting;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Sourcefli\SnapshotTesting\Contracts\ISnapshotConnection;
use Sourcefli\SnapshotTesting\SnapshotConnection as SnapshotConnection;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SnapshotTestingServiceProvider extends PackageServiceProvider implements DeferrableProvider
{
	public function provides(): array
	{
		return [
			'snapshot-testing',
			ISnapshotConnection::class,
		];
	}

	public function configurePackage(Package $package): void
	{
		$package
			->name(snapshotPackageName())
			->hasConfigFile();
	}

	public function packageRegistered()
	{
		if ($this->app->runningUnitTests()) {
			$this->app->singleton('snapshot-testing', fn ($app) => new SnapshotTesting);
			$this->app->singleton(ISnapshotConnection::class, fn ($app) => new SnapshotConnection);
		}
	}

	public function packageBooted()
	{
		if ($this->app->runningUnitTests()) {
			$this->app->booted(function (Application $app) {
				$app['config']->set(
					'filesystems.disks.snapshot-testing',
					snapshotPackageConfig('disk.snapshot-testing')
				);
			});
		}
	}
}
