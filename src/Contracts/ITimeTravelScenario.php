<?php

namespace Sourcefli\SnapshotTesting\Contracts;

use Carbon\CarbonImmutable;
use Sourcefli\SnapshotTesting\Attributes\SnapshotCategory;

#[SnapshotCategory(self::class, self::CATEGORY)]
interface ITimeTravelScenario extends IScenario
{
	const CATEGORY = 'time_traveler_scenarios';

	/**
	 * @return CarbonImmutable
	 */
	public function getTimeTravelDate(): CarbonImmutable;
}
