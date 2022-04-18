<?php

namespace Sourcefli\SnapshotTesting\Scenarios;

use Sourcefli\SnapshotTesting\Collections\CategoryCollection;
use Sourcefli\SnapshotTesting\Collections\SnapshotCollection;
use SplObjectStorage;

/**
 * @implements ScenarioStorage<ScenarioStorage, CategoryCollection<array-key, SnapshotCollection>>
 */
class ScenarioStorage extends SplObjectStorage
{
	public function getCategoryCollection(string|SnapshotScenario $scenario): ?CategoryCollection
	{
		foreach ($this as $existingScenario) {
			if (get_class($scenario) === get_class($existingScenario)) {
				return $this->getInfo();
			}
		}

		return null;
	}

	public function offsetGet($object): ?SnapshotScenario
	{
		return parent::offsetGet($object);
	}

	public function getInfo(): CategoryCollection
	{
		return parent::getInfo();
	}

	public function getHash($object): string
	{
		return get_class($object);
	}
}
