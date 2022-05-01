<?php


it('gets the package name')
	->expect(fn () => snapshotPackageName())
	->toBe('laravel-snapshot-testing');


it('gets the package home')
	->expect(fn () => snapshotPackageHome())
	->toBe(str_replace('/tests/Feature','',__DIR__));


it('gets the package config name')
	->expect(fn () => snapshotPackageConfigName())
	->toBe('snapshot-testing');


it('gets the package config file path')
	->expect(fn () => snapshotPackageConfigFilePath())
	->toBe(snapshotPackageHome().'/config/'.snapshotPackageConfigName().'.php');


it('gets all package configurations', function () {
	expect(snapshotPackageConfig())->toBe(require snapshotPackageConfigFilePath());
});

it('gets specific package configurations', function () {
	expect(snapshotPackageConfig('database.connection'))
		->toBe('testing')
		->and(snapshotPackageConfig('disk.snapshot-testing.root'))
		->toBe(storage_path('framework/cache/snapshots'));
});
