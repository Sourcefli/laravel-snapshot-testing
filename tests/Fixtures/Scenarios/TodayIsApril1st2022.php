<?php

namespace Sourcefli\SnapshotTesting\Tests\Fixtures\Scenarios;

use Carbon\CarbonImmutable;
use Sourcefli\SnapshotTesting\Contracts\ITimeTravelScenario;
use function Sourcefli\CarbonHelpers\carbonImmutable;

class TodayIsApril1st2022 implements ITimeTravelScenario
{
	public function getTimeTravelDate(): CarbonImmutable
	{
		return carbonImmutable('2022-04-01');
	}
}
