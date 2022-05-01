<?php
if (! function_exists('snapshotPackageName')) {
	function snapshotPackageName(): string
	{
		static $home;

		return $home ??= basename(snapshotPackageHome());
	}
}

if (! function_exists('snapshotPackageHome')) {
	function snapshotPackageHome(): string
	{
		static $name;

		return $name ??= tap(collect(explode(DIRECTORY_SEPARATOR, __DIR__)))->pop()->join(DIRECTORY_SEPARATOR);
	}
}

if (! function_exists('snapshotPackageConfig')) {
	function snapshotPackageConfig(?string $attribute = null): mixed
	{
		$path = rtrim(sprintf('%s.%s', snapshotPackageConfigName(), $attribute ?? ''), '.');

		return config($path);
	}
}

if (! function_exists('setSnapshotPackageConfig')) {
	function setSnapshotPackageConfig(string $path, string|array $attributes): mixed
	{
		if (! str_starts_with($path, snapshotPackageConfigName())) {
			$path = snapshotPackageConfigName().'.'.$path;
		}

		return config([$path => $attributes]);
	}
}

if (! function_exists('snapshotPackageConfigName')) {
	function snapshotPackageConfigName(): string
	{
		return 'snapshot-testing';
	}
}

if (! function_exists('snapshotPackageConfigFilePath')) {
	function snapshotPackageConfigFilePath(): string
	{
		return snapshotPackageHome().'/config/'.snapshotPackageConfigName().'.php';
	}
}
