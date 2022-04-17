<?php

namespace Sourcefli\SnapshotTesting\Contracts;

use Carbon\CarbonImmutable;
use Sourcefli\SnapshotTesting\Attributes\SnapshotCategory;

#[SnapshotCategory(self::class)]
interface ITimeTravelScenario
{
	/**
	 * @return CarbonImmutable
	 */
	public function getTimeTravelDate(): CarbonImmutable;
}
