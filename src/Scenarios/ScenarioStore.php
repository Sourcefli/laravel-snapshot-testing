<?php

namespace Sourcefli\SnapshotTesting\Scenarios;

use Sourcefli\SnapshotTesting\Collections\SnapshotCategoriesCollection;
use Sourcefli\SnapshotTesting\Collections\SnapshotCollection;
use SplObjectStorage;

/**
 * @implements ScenarioStore<ScenarioStore, SnapshotCategoriesCollection<array-key, SnapshotCollection>>
 */
class ScenarioStore extends SplObjectStorage
{
	public function getCategoryCollection(string|SnapshotScenario $scenario): ?SnapshotCategoriesCollection
	{
		$scenario = is_string($scenario) ? $scenario : get_class($scenario);

		foreach ($this as $existingScenario) {
			if ($scenario === get_class($existingScenario)) {
				return $this->getInfo();
			}
		}

		return null;
	}

	public function offsetGet($object): ?SnapshotScenario
	{
		return parent::offsetGet($object);
	}

	public function getInfo(): SnapshotCategoriesCollection
	{
		return parent::getInfo();
	}

	public function getHash($object): string
	{
		return get_class($object);
	}
}
