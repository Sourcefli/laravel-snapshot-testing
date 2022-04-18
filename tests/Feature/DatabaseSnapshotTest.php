<?php

use Sourcefli\SnapshotTesting\Facades\SnapshotTesting;
use Sourcefli\SnapshotTesting\Tests\Fixtures\Scenarios;

it('can use a database snapshot on demand', function () {
	$snapshots = SnapshotTesting::usingScenario(\Sourcefli\SnapshotTesting\Scenarios\Examples\TodayIsMarch3rd2021::class)->getSnapshots();

	$this->assertSame(
		'2021-03-03',
		now()->toDateString(),
	);
});
