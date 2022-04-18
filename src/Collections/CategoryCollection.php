<?php

namespace Sourcefli\SnapshotTesting\Collections;

use Illuminate\Support\Collection;
use Sourcefli\SnapshotTesting\Contracts\IDatabaseSnapshot;
use Sourcefli\SnapshotTesting\Contracts\IScenario;

/**
 * @implements Collection<array-key, SnapshotCollection>
 */
class CategoryCollection extends Collection
{
	/**
	 * @var string
	 */
	protected string $category;

	/**
	 * @param  string  $category
	 *
	 * @return static
	 */
	public function setCategory(string $category): static
	{
		$this->category = $category;

		return $this;
	}

	public function addSnapshotCollection(SnapshotCollection $snapshots): static
	{
		if ($this->hasScenario($scenario = $snapshots->getScenario())) {
			$this->findByScenario($scenario)->addSnapshots($snapshots);
		} else {
			$this->add($snapshots);
		}

		return $this;
	}

	/**
	 * @param  IScenario  $scenario
	 *
	 * @return bool
	 */
	public function hasScenario(IScenario $scenario): bool
	{
		return $this->contains(fn (SnapshotCollection $snapshots) => get_class($snapshots->getScenario()) === get_class($scenario));
	}

	/**
	 * @param  string|IDatabaseSnapshot  $snapshot
	 *
	 * @return bool
	 */
	public function hasSnapshot(string|IDatabaseSnapshot $snapshot): bool
	{
		return $this->contains(fn (SnapshotCollection $snapshots) => $snapshots->hasSnapshot($snapshot));
	}

	/**
	 * @param  IScenario  $scenario
	 *
	 * @return null|SnapshotCollection
	 */
	public function findByScenario(IScenario $scenario): ?SnapshotCollection
	{
		return $this->first(fn (SnapshotCollection $snapshots) => get_class($snapshots->getScenario()) === get_class($scenario));
	}
}
