<?php

namespace Sourcefli\SnapshotTesting\Contracts;

use Carbon\CarbonImmutable;
use Sourcefli\SnapshotTesting\Attributes\SnapshotCategory;

#[SnapshotCategory(self::class, 'time_travelers')]
interface ITimeTravelScenario extends IScenario
{
	/**
	 * @return CarbonImmutable
	 */
	public function getTimeTravelDate(): CarbonImmutable;
}
