<?php

namespace Sourcefli\SnapshotTesting\Collections;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Sourcefli\SnapshotTesting\Contracts\IDatabaseSnapshot;
use Sourcefli\SnapshotTesting\Contracts\IScenario;
use Sourcefli\SnapshotTesting\Exceptions\SnapshotTestingException;
use Sourcefli\SnapshotTesting\Facades\SnapshotTesting;

/**
 * @implements Collection<array-key, SnapshotCollection>
 */
class CategoryCollection extends Collection
{
	public function addSnapshotCollection(SnapshotCollection $snapshots): static
	{
		$scenario = $snapshots->getScenario();

		$this->assertValidCategoryKeys($categories = $scenario->getCategories());

		foreach ($categories as $category) {
			match (TRUE) {
				$this->hasScenario($scenario) => $this->findByScenario($scenario)->addSnapshots($snapshots),
				$this->hasCategory($category) => $this->getCategory($category)->addSnapshots($snapshots),
				default => $this[$category] = $snapshots,
			};
		}

		return $this;
	}

	public function getCategory(string $category): ?SnapshotCollection
	{
		$this->assertValidCategoryKeys($category);

		return $this->get($category);
	}

	/**
	 * @param  string|IScenario  $scenario
	 *
	 * @return bool
	 */
	public function hasScenario(string|IScenario $scenario): bool
	{
		if (is_object($scenario)) {
			$scenario = get_class($scenario);
		}

		return $this->contains(fn (SnapshotCollection $snapshots) => get_class($snapshots->getScenario()) === $scenario);
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
	 * @param  string|IScenario  $scenario
	 *
	 * @return null|SnapshotCollection
	 */
	public function findByScenario(string|IScenario $scenario): ?SnapshotCollection
	{
		if (is_object($scenario)) {
			$scenario = get_class($scenario);
		}

		return $this->first(fn (SnapshotCollection $snapshots) => get_class($snapshots->getScenario()) === $scenario);
	}

	public function hasCategory(string $category): bool
	{
		return $this->keys()->contains($category);
	}

	/**
	 * @param  string|array  $categories
	 *
	 * @return void
	 */
	protected function assertValidCategoryKeys(string|array $categories = []): void
	{
		$subjects = $this->keys()->merge(Arr::wrap($categories))->unique();

		foreach ($subjects as $category) {
			if (! SnapshotTesting::hasCategory($category)) {
				throw SnapshotTestingException::invalidSnapshotCategory($category);
			}
		}
	}

	public function getValidCategories(): Collection
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return SnapshotTesting::collectCategorizedContractInfo()->flatMap->pluck('category');
	}
}
