<?php

namespace Sourcefli\SnapshotTesting\Tests\Fixtures\Scenarios;

use Carbon\CarbonImmutable;
use Sourcefli\SnapshotTesting\Scenarios\TimeTravel\ITimeTravelScenario;
use function Sourcefli\CarbonHelpers\carbonImmutable;

class TodayIsMarch3rd2021 implements ITimeTravelScenario
{
	public function getTimeTravelDate(): CarbonImmutable
	{
		return carbonImmutable('2021-03-03');
	}
}
