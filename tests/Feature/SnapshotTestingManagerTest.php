<?php

use Illuminate\Contracts\Filesystem\Filesystem;
use Sourcefli\SnapshotTesting\Contracts\ISnapshotConnection;
use Sourcefli\SnapshotTesting\Contracts\ITimeTravelScenario;
use Sourcefli\SnapshotTesting\Facades\SnapshotTesting;
use Sourcefli\SnapshotTesting\Tests\Fixtures\Scenarios;

beforeEach(function () {
	config(['snapshot-testing.scenarios.time_travelers' => [
		\Sourcefli\SnapshotTesting\Scenarios\Examples\TodayIsMarch3rd2021::class,
		\Sourcefli\SnapshotTesting\Scenarios\Examples\TodayIsApril1st2022::class,
	]]);
});

it('has time travel scenarios when configured', function () {
	$scenarios = SnapshotTesting::getTimeTravelScenarios();

	$this->assertCount(2, $scenarios);

	foreach ($scenarios as $scenario) {
		$this->assertContains(ITimeTravelScenario::class, class_implements($scenario));
	}
});

it('has a snapshot connection', function () {
	$snapshotConnection = SnapshotTesting::getConnection();

	$this->assertContains(ISnapshotConnection::class, class_implements($snapshotConnection));
});

it('has a disk', function () {
	$disk = SnapshotTesting::getDisk();

	$this->assertContains(Filesystem::class, class_implements($disk));
	$this->assertStringEndsWith('storage/framework/cache/snapshots', $disk->getConfig()['root']);
});

it('provides scenario contracts that are currently available', function () {
	$currentlyAvailable = [
		ITimeTravelScenario::class
	];

	$contracts = SnapshotTesting::collectScenarioContracts()->values()->all();

	$this->assertSame($currentlyAvailable, $contracts);
});
