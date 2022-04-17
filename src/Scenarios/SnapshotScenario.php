<?php

namespace Sourcefli\SnapshotTesting\Scenarios;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Filesystem\Filesystem as IFilesystem;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Filesystem\Filesystem;
use League\Flysystem\FilesystemAdapter as IFilesystemAdapter;

abstract class SnapshotScenario
{
	/**
	 * Should point to a directory that holds
	 * various SQLite files, one for each
	 * snapshot in this scenario
	 *
	 * @var IFilesystem|IFilesystemAdapter
	 */
	protected IFilesystem|IFilesystemAdapter $disk;

	/**
	 * @var ISnapshotConnection
	 */
	protected ISnapshotConnection $snapshotConnection;

	/**
	 * @return IDatabaseSnapshot[]
	 */
	abstract protected function getSnapshots(): array;

	/**
	 * @return CarbonImmutable|null
	 */
	abstract public function scenarioStartsAt(): ?CarbonImmutable;

	/**
	 * @return CarbonImmutable|null
	 */
	abstract public function scenarioEndsAt(): ?CarbonImmutable;

	/**
	 * \App\Domain\Testing\Snapshots\RangeUnderTest\SnapshotScenario constructor
	 *
	 * @param  ISnapshotConnection  $connectionName
	 * @param  IFilesystem|IFilesystemAdapter  $disk
	 */
	public function __construct(
		ISnapshotConnection $connectionName,
		IFilesystem|IFilesystemAdapter $disk,
	) {
		$this->setSnapshotConnection($connectionName);
		$this->setDisk($disk);
	}

	/**
	 * @param  IDatabaseSnapshot  $databaseSnapshot
	 */
	public function applySnapshotData(IDatabaseSnapshot $databaseSnapshot): void
	{
		if ($databaseSnapshot->usesSQLiteConnection() && ! $this->snapshotConnection->getDatabase() instanceof SQLiteConnection) {
			//			$this->snapshotConnection
			throw new \LogicException('Snapshot uses in memory connection');
		}

		$this->snapshotConnection->getDatabase()->statement(
			$this->disk->get($databaseSnapshot->getFilename())
		);
	}

	/**
	 * @return DishMySqlSnapshotConnection
	 */
	public function getSnapshotConnection(): DishMySqlSnapshotConnection
	{
		return $this->snapshotConnection;
	}

	/**
	 * @return \App\Domain\Testing\Snapshots\Scenarios\SnapshotScenario
	 */
	public function getCurrentSnapshot(): SnapshotScenario
	{
		return $this->currentSnapshot;
	}

	/**
	 * @return IFilesystemAdapter|Filesystem
	 */
	public function getDisk(): IFilesystemAdapter|Filesystem
	{
		return $this->disk;
	}

	/**
	 * @return int
	 */
	public function getTotalDays(): int
	{
		return $this->scenarioStartsAt()->daysUntil($this->scenarioEndsAt())->count();
	}

	/**
	 * @param  ISnapshotConnection  $snapshotConnection
	 *
	 * @return static
	 */
	public function setSnapshotConnection(ISnapshotConnection $snapshotConnection): static
	{
		$this->snapshotConnection = $snapshotConnection;

		return $this;
	}

	/**
	 * @param  IFilesystem|IFilesystemAdapter  $disk
	 *
	 * @return static
	 */
	public function setDisk(IFilesystem|IFilesystemAdapter $disk): static
	{
		$this->disk = $disk;

		return $this;
	}

	/**
	 * @param  string  $snapshotClass
	 * @param  array  $params
	 * @param  bool  $refreshDatabase
	 *
	 * @return IDatabaseSnapshot
	 */
	public function useSnapshot(string $snapshotClass, array $params = [], bool $refreshDatabase = false): IDatabaseSnapshot
	{
		if (! is_a($snapshotClass, IDatabaseSnapshot::class, true)) {
			$snapshotClass = class_basename($snapshotClass);

			throw new InvalidArgumentException("[{$snapshotClass}] must be an implementation of the IDatabaseSnapshot contract");
		}

		/** @var IDatabaseSnapshot $snapshot */
		$snapshot = empty($params) ? new $snapshotClass : new $snapshotClass(...$params);

		return $snapshot->applyDatabaseState($this->snapshotConnection, $refreshDatabase);
	}
}
