<?php

namespace Sourcefli\SnapshotTesting\Scenarios;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use ReflectionAttribute;
use ReflectionClass;
use Sourcefli\SnapshotTesting\Attributes\SnapshotCategory;
use Sourcefli\SnapshotTesting\Collections\CategoryCollection;
use Sourcefli\SnapshotTesting\Collections\SnapshotCollection;
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
		$this->snapshotManager = app('snapshot-testing');
	}

	public function collectCategoriesFromContractsImplemented(): Collection
	{
		return collect(class_implements($this))
			->intersect($this->snapshotManager->collectScenarioContracts())
			->unique()
			->map(fn (string $scenarioContract) => new ReflectionClass($scenarioContract))
			->filter(fn (ReflectionClass $scenarioRflx) => count($scenarioRflx->getAttributes(SnapshotCategory::class)))
			->flatMap(fn (ReflectionClass $scenarioRflx) => array_map(
				fn (ReflectionAttribute $attribute) => $attribute->newInstance()->getCategory(), $scenarioRflx->getAttributes(SnapshotCategory::class))
			)
			->unique();
	}

	public function getCategories(): array
	{
		return $this->setCategories()->categories;
	}

	public function hasSnapshot(string $category, string|IDatabaseSnapshot $snapshot): bool
	{
		return $this->categories[$category]?->hasSnapshot($snapshot) ?? false;
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

	protected function setCategories(array $snapshots = []): static
	{
		foreach ($this->collectCategoriesFromContractsImplemented() as $category) {
			$snapshots = SnapshotCollection::make(array_unique([
				...$this->snapshotManager->getConfig(sprintf('scenarios.%s.%s', $category, static::class), []),
				...$this->snapshotDeclarations(),
				...$snapshots
			]))->setScenario($this);

			if (! Arr::has($this->categories, $category)) {
				$this->categories[$category] = CategoryCollection::make([$snapshots])->setCategory($category);
				continue;
			}

			$this->categories[$category]->addSnapshotCollection($snapshots);
		}

		return $this;
	}
}
