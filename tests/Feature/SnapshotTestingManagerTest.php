<?php

use Sourcefli\SnapshotTesting\Scenarios\TimeTravel\ITimeTravelScenario;
use Sourcefli\SnapshotTesting\SnapshotTesting;
use Sourcefli\SnapshotTesting\Tests\Fixtures\Scenarios;

beforeEach(function () {
	config(['snapshot-testing.scenarios.time_travelers' => [
		Scenarios\TodayIsMarch3rd2021::class,
		Scenarios\TodayIsApril1st2022::class,
	]]);
});

it('has time travel scenarios', function () {
	$scenarios = app(SnapshotTesting::class)->getTimeTravelScenarios();

	$this->assertCount(2, $scenarios);

	foreach ($scenarios as $scenario) {
		$this->assertContains(ITimeTravelScenario::class, class_implements($scenario));
	}
});
