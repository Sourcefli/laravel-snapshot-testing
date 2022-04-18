<?php

namespace Sourcefli\SnapshotTesting\Contracts;

use Sourcefli\SnapshotTesting\Attributes\SnapshotCategory;

/**
 * @internal
 *
 * Use contracts having the {@see SnapshotCategory} attribute
 */
interface IScenario
{
	/**
	 * @return IDatabaseSnapshot[]
	 */
	public function getSnapshots(): array;

	/**
	 * @param  IDatabaseSnapshot  $databaseSnapshot
	 */
	public function seedSnapshotData(IDatabaseSnapshot $databaseSnapshot): void;

	/**
	 * @return void
	 */
	public function setupTestEnvironment(): void;
}
