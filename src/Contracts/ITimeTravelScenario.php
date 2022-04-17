<?php

namespace Sourcefli\SnapshotTesting\Contracts;

use Carbon\CarbonImmutable;
use Sourcefli\SnapshotTesting\Attributes\SnapshotCategory;

#[SnapshotCategory(self::class)]
interface ITimeTravelScenario extends IScenario
{
	/**
	 * @return CarbonImmutable
	 */
	public function getTimeTravelDate(): CarbonImmutable;
}
