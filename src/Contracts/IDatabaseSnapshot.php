<?php

namespace Sourcefli\SnapshotTesting\Contracts;

use Carbon\CarbonInterface;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;

interface IDatabaseSnapshot
{
	/**
	 * @param  ISnapshotConnection  $connection
	 *
	 * @return static
	 */
	public function applyDatabaseState(ISnapshotConnection $connection): static;

	/**
	 * @return ConnectionInterface|Connection
	 */
	public function getDatabaseConnection(): ConnectionInterface|Connection;

	/**
	 * @return CarbonInterface|null
	 */
	public function dataEndsOn(): ?CarbonInterface;

	/**
	 * @return CarbonInterface|null
	 */
	public function dataStartsOn(): ?CarbonInterface;

	/**
	 * @return string
	 */
	public function getDirectoryPath(): string;

	/**
	 * @return string
	 */
	public function getFilename(): string;

	/**
	 * @return string
	 */
	public function getSnapshotAlias(): string;

	/**
	 * If applicable
	 *
	 * @param  ISnapshotConnection  $snapshotConnection
	 *
	 * @return void
	 */
	public function runSQLDump(ISnapshotConnection $snapshotConnection): void;

	/**
	 * @return int
	 */
	public function totalRecordsValid(): int;

	/**
	 * @return int
	 */
	public function totalRecordsInvalid(): int;
}
