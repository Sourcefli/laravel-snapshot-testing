<?php

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Connection;
use Sourcefli\SnapshotTesting\Contracts\ISnapshotConnection;
use Sourcefli\SnapshotTesting\Facades\SnapshotTesting;
use function PHPUnit\Framework\assertSame;

it('has a disk')
	->expect(fn () => SnapshotTesting::getDisk())->toBeInstanceOf(Filesystem::class);


it('has a connection')
	->expect(fn () => SnapshotTesting::getConnection())
	->toBeInstanceOf(ISnapshotConnection::class)
	->and(fn () => SnapshotTesting::getConnection()->getDatabaseConnection())
	->toBeInstanceOf(Connection::class);


it ('uses the connection defined in the configuration', function () {
    $connection = SnapshotTesting::getConnection()->getDatabaseConnection();

	assertSame(
		snapshotPackageConfig('database.connection'),
		$connection->getName()
	);

	# make a runtime update to the connection name
	config(['database.connections.sqlite' => [
		'driver' => 'sqlite',
		'database' => ':memory:'
	]]);
	setSnapshotPackageConfig('database.connection', 'sqlite');
	\DB::purge();

	# make sure we're now using the new connection
	assertSame(
		snapshotPackageConfig('database.connection'),
		SnapshotTesting::getConnection()->getDatabaseConnection()->getName()
	);
});
