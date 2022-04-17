<?php

namespace Sourcefli\SnapshotTesting\Scenarios\TimeTravel;

use Carbon\CarbonImmutable;

interface ITimeTravelScenario
{
	/**
	 * @return CarbonImmutable
	 */
	public function getTimeTravelDate(): CarbonImmutable;
}
