<?php

namespace Sourcefli\SnapshotTesting\Scenarios\Examples;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Sourcefli\SnapshotTesting\Contracts\ITimeTravelScenario;
use Sourcefli\SnapshotTesting\Scenarios\SnapshotScenario;
use Sourcefli\SnapshotTesting\Snapshots\Examples\UsersHaveManyPostsPerMonth;
use Sourcefli\SnapshotTesting\Snapshots\Examples\UsersHaveNoUsername;
use function Sourcefli\CarbonHelpers\carbonImmutable;

class TodayIsApril1st2022 extends SnapshotScenario implements ITimeTravelScenario
{
	public function getTimeTravelDate(): CarbonImmutable
	{
		return carbonImmutable('2022-04-01');
	}

	public function setup(): void
	{
		Date::setTestNow($this->getTimeTravelDate());
	}

	public function snapshotDeclarations(): array
	{
		return [
			UsersHaveNoUsername::class,
			UsersHaveManyPostsPerMonth::class
		];
	}
}
