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
	protected array $categories;

	public function __construct()
	{
		$this->snapshotManager = app('snapshot-testing');

		$this->addScenariosByCategory();

//		foreach ($this->snapshotManager->getConfig('scenarios') as $key => $value) {
//			if (! is_array($value)) {
//				continue;
//			}
//
//			dump(['k' => $key, 'v' => $value]);
//		}
	}

	public function getCategories(): Collection
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

	public function getSnapshots(): array
	{
		return $this->snapshots;
	}

	public function seedSnapshotData(IDatabaseSnapshot $databaseSnapshot): void
	{
		if (! Arr::has($this->snapshots, $databaseSnapshot::class)) {
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

	protected function addScenariosByCategory(array $snapshots = [])
	{
		foreach ($this->getCategories() as $category) {
			$snapshots = SnapshotCollection::make(array_unique([
				...$this->snapshotManager->getConfig(sprintf('scenarios.%s.%s', $category, static::class), []),
				...$this->snapshotDeclarations(),
				...$snapshots
			]))->setScenario($this);

			if (! Arr::has($this->categories, $category)) {
				$this->categories[$category] = CategoryCollection::make([$snapshots])->setCategory($category);
				continue;
			}

			$this->categories[$category]->addSnapshots($snapshots);
		}


	}
}
