<?php

use Sourcefli\SnapshotTesting\Facades\SnapshotTesting;
use Sourcefli\SnapshotTesting\Scenarios;

it('can use a database snapshot on demand', function () {
	SnapshotTesting::usingScenario(Scenarios\Examples\TodayIsMarch3rd2021::class)->getCategories();

	$this->assertSame(
		'2021-03-03',
		now()->toDateString(),
	);
});
