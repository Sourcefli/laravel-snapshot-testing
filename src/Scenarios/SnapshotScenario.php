<?php

namespace Sourcefli\SnapshotTesting\Scenarios;

use Illuminate\Support\Arr;
use Sourcefli\SnapshotTesting\Contracts\IDatabaseSnapshot;
use Sourcefli\SnapshotTesting\Contracts\IScenario;
use Sourcefli\SnapshotTesting\Exceptions\SnapshotTestingException;
use Sourcefli\SnapshotTesting\SnapshotTesting as SnapshotTestingManager;

abstract class SnapshotScenario implements IScenario
{
	/**
	 * @var SnapshotTestingManager
	 */
	protected SnapshotTestingManager $snapshotManager;

	/**
	 * @var IDatabaseSnapshot[]
	 */
	protected array $snapshots;

	public function __construct()
	{
		$this->snapshotManager = app('snapshot-testing');

		$this->snapshots = array_map(fn (string $snapshotClass) => [
			$snapshotClass => app($snapshotClass)
		], $this->relatedSnapshots());
	}

	/**
	 * @param  IDatabaseSnapshot  $databaseSnapshot
	 */
	public function seedSnapshotData(IDatabaseSnapshot $databaseSnapshot): void
	{
		if (! Arr::has($this->snapshots, $databaseSnapshot::class)) {
			throw SnapshotTestingException::snapshotNotFound($databaseSnapshot, $this);
		}

		$this->snapshotManager->getConnection()->setCurrentSnapshot($databaseSnapshot);

		$databaseSnapshot->applyDatabaseState();
	}
}
