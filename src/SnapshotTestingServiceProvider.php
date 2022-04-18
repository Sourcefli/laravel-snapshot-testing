<?php

namespace Sourcefli\SnapshotTesting;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\DB;
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
			->name('laravel-snapshot-testing')
			->hasConfigFile('snapshot-testing');
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
			config(['filesystems.disks.snapshot-testing' => config('snapshot-testing.disk.snapshot-testing')]);

			if (config('snapshot-testing.database.is_default_testing_connection', false)) {
				$snapshotConnection = $this->app[ISnapshotConnection::class]->getDatabase()->getName();

				DB::extend('testing', fn () => DB::connection($snapshotConnection));

				DB::purge('testing');

				config(['database.default' => $snapshotConnection]);
			}
		}
	}
}
