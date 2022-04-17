<?php

namespace Sourcefli\SnapshotTesting;

use JetBrains\PhpStorm\ArrayShape;
use Sourcefli\SnapshotTesting\Exceptions\SnapshotTestingException;
use Sourcefli\SnapshotTesting\Scenarios\TimeTravel\ITimeTravelScenario;

trait HasSnapshotConfig
{
	/**
	 * @param  string|null  $attribute
	 *
	 * @return array|string
	 */
	public function getConfig(?string $attribute = null): array|string
	{
		$path = rtrim(sprintf('snapshot-testing.%s', $attribute ?? ''), '.');

		return $this->config->get($path);
	}

	/**
	 * @return ITimeTravelScenario[]
	 */
	protected function getTimeTravelScenarios(): array
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
