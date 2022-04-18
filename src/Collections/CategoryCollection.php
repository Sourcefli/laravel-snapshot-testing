<?php

namespace Sourcefli\SnapshotTesting\Collections;

use Illuminate\Support\Collection;
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

	public function addSnapshots(SnapshotCollection $snapshots): static
	{
		if ($this->hasSnapshotsForScenario($scenario = $snapshots->getScenario())) {
			$this->findByScenario($scenario)->addSnapshots($snapshots);
		}
	}

	/**
	 * @param  IScenario  $scenario
	 *
	 * @return bool
	 */
	public function hasSnapshotsForScenario(IScenario $scenario): bool
	{
		return $this->contains(fn (SnapshotCollection $snapshots) => get_class($snapshots->getScenario()) === get_class($scenario));
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
