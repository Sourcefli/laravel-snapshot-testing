<?php

namespace Sourcefli\SnapshotTesting\Tests\Fixtures\Scenarios;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Sourcefli\SnapshotTesting\Contracts\ITimeTravelScenario;
use Sourcefli\SnapshotTesting\Scenarios\SnapshotScenario;
use Sourcefli\SnapshotTesting\Tests\Fixtures\Snapshots\UsersHadOnePostPerMonth;
use function Sourcefli\CarbonHelpers\carbonImmutable;

class TodayIsMarch3rd2021 extends SnapshotScenario implements ITimeTravelScenario
{
	public function getTimeTravelDate(): CarbonImmutable
	{
		return carbonImmutable('2021-03-03');
	}

	public function setupTestEnvironment(): void
	{
		Date::setTestNow($this->getTimeTravelDate());
	}

	public function relatedSnapshots(): array
	{
		return [
			UsersHadOnePostPerMonth::class
		];
	}
}
