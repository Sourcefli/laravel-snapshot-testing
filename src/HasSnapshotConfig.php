<?php

namespace Sourcefli\SnapshotTesting;

use Sourcefli\SnapshotTesting\Contracts\ITimeTravelScenario;
use Sourcefli\SnapshotTesting\Exceptions\SnapshotTestingException;

trait HasSnapshotConfig
{
	/**
	 * @param  string|null  $attribute
	 * @param  mixed|null  $default
	 *
	 * @return mixed
	 */
	public function getConfig(?string $attribute = null, mixed $default = null): mixed
	{
		$path = rtrim(sprintf('snapshot-testing.%s', $attribute ?? ''), '.');

		return config($path, $default);
	}

	/**
	 * @return ITimeTravelScenario[]
	 */
	public function getTimeTravelScenarios(): array
	{
		$dateScenarios = $this->getConfig('scenarios.time_travelers');

		foreach ($dateScenarios as $scenario) {
			if (! is_a($scenario, ITimeTravelScenario::class, true)) {
				throw SnapshotTestingException::invalidConfiguration(
					$scenario::class,
					'a class implementing the IDateScenario contract'
				);
			}
		}

		return $dateScenarios;
	}
}
