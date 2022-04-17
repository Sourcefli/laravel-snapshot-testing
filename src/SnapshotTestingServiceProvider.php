<?php

namespace Sourcefli\SnapshotTesting;

use Sourcefli\SnapshotTesting\Commands\SnapshotTestingCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SnapshotTestingServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-snapshot-testing')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-snapshot-testing_table')
            ->hasCommand(SnapshotTestingCommand::class);
    }
}
