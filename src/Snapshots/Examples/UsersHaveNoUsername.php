<?php

namespace Sourcefli\SnapshotTesting\Snapshots\Examples;

use Carbon\CarbonInterface;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Sourcefli\SnapshotTesting\Contracts\IDatabaseSnapshot;
use Sourcefli\SnapshotTesting\Contracts\ISnapshotConnection;

class UsersHaveNoUsername implements IDatabaseSnapshot
{
	public function applyDatabaseState(): static
	{
		// TODO: Implement applyDatabaseState() method.
	}

	public function getDatabaseConnection(): ConnectionInterface|Connection
	{
		// TODO: Implement getDatabaseConnection() method.
	}

	public function dataEndsOn(): ?CarbonInterface
	{
		// TODO: Implement dataEndsOn() method.
	}

	public function dataStartsOn(): ?CarbonInterface
	{
		// TODO: Implement dataStartsOn() method.
	}

	public function getDirectoryPath(): string
	{
		// TODO: Implement getDirectoryPath() method.
	}

	public function getFilename(): string
	{
		// TODO: Implement getFilename() method.
	}

	public function getSnapshotAlias(): string
	{
		// TODO: Implement getSnapshotAlias() method.
	}

	public function runSQLDump(ISnapshotConnection $snapshotConnection): void
	{
		// TODO: Implement runSQLDump() method.
	}

	public function totalRecordsValid(): int
	{
		// TODO: Implement totalRecordsValid() method.
	}

	public function totalRecordsInvalid(): int
	{
		// TODO: Implement totalRecordsInvalid() method.
	}
}
