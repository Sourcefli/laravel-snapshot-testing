<?php

use Sourcefli\SnapshotTesting\Facades\SnapshotTesting;
use Sourcefli\SnapshotTesting\Scenarios;
use Sourcefli\SnapshotTesting\Snapshots;

it('time travels using a time travel scenario', function () {
	SnapshotTesting::usingScenario(Scenarios\Examples\TodayIsMarch3rd2021::class);

	$this->assertSame(
		'2021-03-03',
		now()->toDateString(),
	);
});
