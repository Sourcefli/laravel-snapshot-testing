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
	/** @noinspection PhpVoidFunctionResultUsedInspection */
	public function __construct($items = [])
	{
		parent::__construct(array_map(fn (string|IDatabaseSnapshot $snapshot) =>
			with($this->assertSnapshotClass($snapshot),
				fn () => is_string($snapshot) ? app($snapshot) : $snapshot
			), $items)
		);
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
		$this->scenario = is_string($scenario) ? app($scenario) : $scenario;

		return $this;
	}

	public function addSnapshots(SnapshotCollection $snapshots)
	{
		foreach ($snapshots as $snapshot) {
			if ($this->hasSnapshot($snapshot)) {
				continue;
			}
		}
	}

	public function hasSnapshot(IDatabaseSnapshot $snapshot): bool
	{
		return $this->contains(fn (IDatabaseSnapshot $existingSnapshot) => get_class($existingSnapshot) === get_class($snapshot));
	}

	protected function assertSnapshotClass(string|object $snapshot): void
	{
		if (! is_a($snapshot, IDatabaseSnapshot::class, true)) {
			throw SnapshotTestingException::classInvalid($snapshot, IDatabaseSnapshot::class);
		}
	}
}
