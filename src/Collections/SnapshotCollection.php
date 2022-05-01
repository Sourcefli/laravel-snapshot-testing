<?php

namespace Sourcefli\SnapshotTesting\Collections;

use Illuminate\Support\Collection;
use Sourcefli\SnapshotTesting\Contracts\IDatabaseSnapshot;
use Sourcefli\SnapshotTesting\Contracts\IScenario;
use Sourcefli\SnapshotTesting\Exceptions\SnapshotTestingException;

/**
 * @implements Collection<array-key, IDatabaseSnapshot>
 */
class SnapshotCollection extends Collection
{
	public function __construct($items = [])
	{
		parent::__construct(array_map(
			[$this, 'instantiateSnapshot'],
//			fn (string|IDatabaseSnapshot $snapshot) => $this->instantiateSnapshot($snapshot),
			static::unwrap($items)
		));
	}

	/**
	 * @var IScenario
	 */
	protected IScenario $scenario;

	public function getScenario(): ?IScenario
	{
		return $this->scenario ?? null;
	}

	public function setScenario(string|IScenario $scenario): static
	{
		if (is_string($scenario)) {
			/** @var IScenario $scenario */
			$scenario = $scenario::make();
		}

		$this->scenario = $scenario;

		return $this;
	}

	public function addSnapshots(SnapshotCollection $snapshots): static
	{
		foreach ($snapshots as $snapshot) {
			if (! $this->hasSnapshot($snapshot)) {
				$this->add($snapshot);
			}
		}

		return $this;
	}

	public function hasSnapshot(string|IDatabaseSnapshot $snapshot): bool
	{
		if (is_object($snapshot)) {
			$snapshot = get_class($snapshot);
		}

		return $this->contains(fn (IDatabaseSnapshot $existingSnapshot) => get_class($existingSnapshot) === $snapshot);
	}

	protected function instantiateSnapshot(string|object $snapshot): IDatabaseSnapshot
	{
		if (! is_a($snapshot, IDatabaseSnapshot::class, true)) {
			throw SnapshotTestingException::classInvalid($snapshot, IDatabaseSnapshot::class);
		}

		return is_string($snapshot) ? app($snapshot) : $snapshot;
	}

	public static function forScenario(IScenario|string $scenario, array $snapshots = []): static
	{
		return (new static($snapshots))->setScenario($scenario);
	}
}
