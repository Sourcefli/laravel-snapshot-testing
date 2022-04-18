<?php

declare(strict_types=1);

namespace Sourcefli\SnapshotTesting\Scenarios;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Sourcefli\SnapshotTesting\Collections\CategoryCollection;
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
	 * @var CategoryCollection[]
	 */
	protected array $categories = [];

	public function __construct()
	{
		app('snapshot-testing')->addScenario($this);
	}

	public function addCategory(string $category): static
	{
		$this->categories = array_unique([
			...$this->categories,
			$category
		]);

		return $this;
	}

	public function collectOwnedCategories(): Collection
	{
		return $this->snapshotManager->getCategoriesForScenario($this);
	}

	public function getCategories(): array
	{
		return $this->categories;
	}

	public function hasSnapshot(string $category, string|IDatabaseSnapshot $snapshot): bool
	{
		if (is_object($snapshot)) {
			$snapshot = get_class($snapshot);
		}

		foreach ($this->snapshotManager->getSnapshotsForScenario($category, $this) as $_snapshot) {
			if (get_class($_snapshot) === $snapshot) {
				return true;
			}
		}

		return false;
	}

	public function seedSnapshotData(string $category, IDatabaseSnapshot $databaseSnapshot): void
	{
		if (! Arr::has($this->categories[$category] ?? [], $databaseSnapshot::class)) {
			throw SnapshotTestingException::snapshotNotFound($databaseSnapshot, $this);
		}

		$this->snapshotManager->getConnection()->setCurrentSnapshot($databaseSnapshot);

		$databaseSnapshot->applyDatabaseState();
	}

	/** Takes precedence over any declaration that may be duplicated within the [config.snapshot-testing.scenarios] array */
	public function snapshotDeclarations(): array
	{
		return [];
	}

	public function setSnapshotManager(SnapshotTestingManager $snapshotManager): static
	{
		$this->snapshotManager = $snapshotManager;

		return $this;
	}

	public function hasCategory(string $category): bool
	{
		return array_key_exists($category, $this->categories);
	}
}
