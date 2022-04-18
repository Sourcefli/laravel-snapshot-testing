<?php

namespace Sourcefli\SnapshotTesting;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Sourcefli\SnapshotTesting\Contracts\IDatabaseSnapshot;
use Sourcefli\SnapshotTesting\Contracts\IScenario;
use Sourcefli\SnapshotTesting\Contracts\ITimeTravelScenario;

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

	protected function getConfiguredScenarios(string|IScenario $scenario): array
	{
		$category = $scenario::CATEGORY;

		if (is_object($scenario)) {
			$scenario = get_class($scenario);
		}

		if (! is_a($scenario, IScenario::class, true)) {
			$scenario = class_basename($scenario);

			throw new InvalidArgumentException("[{$scenario}] must be an implementation of \Sourcefli\SnapshotTesting\Contracts\IScenario");
		}

		return $this->getConfigurationClassesByContract($category, IScenario::class);
	}

	protected function getConfiguredSnapshots(string $category): array
	{
		return $this->getConfigurationClassesByContract($category, IDatabaseSnapshot::class);
	}

	/**
	 * @return ITimeTravelScenario[]
	 */
	public function getTimeTravelScenarios(): array
	{
		return $this->getConfiguredScenarios(ITimeTravelScenario::class);
	}

	/**
	 * @param  string  $category
	 * @param  string  $contract
	 *
	 * @return array
	 */
	private function getConfigurationClassesByContract(string $category, string $contract): array
	{
		$configScenarios = Arr::divide($this->getConfig("scenarios.$category"));

		$filterScenarios = fn (array $scenarios) => array_filter(Arr::flatten($scenarios), fn ($s) => is_a($s, $contract, true));

		$results = [];
		foreach ($configScenarios as $scenarioKey => $scenarioValue) {
			$results = [
				...$results,
				...$filterScenarios(array_merge(Arr::wrap($scenarioKey), Arr::wrap($scenarioValue)))
			];
		}

		if ($contract !== IDatabaseSnapshot::class) {
			dd($results);
		}
		return $results;
}
}
