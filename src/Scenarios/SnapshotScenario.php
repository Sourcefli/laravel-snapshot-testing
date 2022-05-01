<?php

declare(strict_types=1);

namespace Sourcefli\SnapshotTesting\Scenarios;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Sourcefli\SnapshotTesting\Collections\SnapshotCategoriesCollection;
use Sourcefli\SnapshotTesting\Contracts\IDatabaseSnapshot;
use Sourcefli\SnapshotTesting\Contracts\IScenario;
use Sourcefli\SnapshotTesting\Exceptions\SnapshotTestingException;
use Sourcefli\SnapshotTesting\Facades\SnapshotTesting;

abstract class SnapshotScenario implements IScenario
{
	/**
	 * @var SnapshotCategoriesCollection[]
	 */
	protected array $categories = [];

	/** no public access. use '::make()'  */
	private function __construct() {}

	public static function make(): static
	{
		return tap(new static, fn ($self) => SnapshotTesting::addScenario($self));
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
		return SnapshotTesting::getCategoriesForScenario($this);
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

		foreach (SnapshotTesting::getSnapshotsForScenario($category, $this) as $_snapshot) {
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

		SnapshotTesting::getConnection()->setCurrentSnapshot($databaseSnapshot);

		$databaseSnapshot->applyDatabaseState();
	}

	/** Takes precedence over any declaration that may be duplicated within the [config.snapshot-testing.scenarios] array */
	public function snapshotDeclarations(): array
	{
		return [];
	}

	public function hasCategory(string $category): bool
	{
		return array_key_exists($category, $this->categories);
	}
}
