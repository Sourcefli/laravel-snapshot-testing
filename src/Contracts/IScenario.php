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
	public function getCategories(): array;

	/**
	 * @param  string  $category
	 * @param  IDatabaseSnapshot  $databaseSnapshot
	 */
	public function seedSnapshotData(string $category, IDatabaseSnapshot $databaseSnapshot): void;

	/**
	 * @return void
	 */
	public function setupTestEnvironment(): void;
}
