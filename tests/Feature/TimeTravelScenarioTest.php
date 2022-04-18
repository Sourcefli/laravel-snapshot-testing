<?php

use Mockery as m;
use Mockery\MockInterface;
use Sourcefli\SnapshotTesting\Contracts\IScenario;
use Sourcefli\SnapshotTesting\Facades\SnapshotTesting;
use Sourcefli\SnapshotTesting\Scenarios;
use Sourcefli\SnapshotTesting\Snapshots;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertSame;

afterAll(fn () => m::close());

it('can declare its own database snapshots for each scenario', function () {
	$scenario = m::mock(Scenarios\Examples\TodayIsMarch3rd2021::class, IScenario::class, function (MockInterface $mock) {
	    $mock->allows([
			'setupTestEnvironment' => null,
			'getSnapshots' => [
				Snapshots\Examples\UsersHadNoUsername::class
			]
		]);
	});

	$snapshots = SnapshotTesting::usingScenario($scenario)->getCategories();

	assertNotEmpty($snapshots);
	assertCount(1, $snapshots);
	assertSame(Snapshots\Examples\UsersHadNoUsername::class, head($snapshots));
});

it('can have no snapshots declared in class when snapshots are declared in the config file', function () {
	config(['snapshot-testing.scenarios.time_travelers' => []]);

	$scenario = m::mock(Scenarios\Examples\TodayIsMarch3rd2021::class, IScenario::class, function (MockInterface $mock) {
		$mock->allows([
			'setupTestEnvironment' => null,
			'getSnapshots' => []
		]);
	});

	assertEmpty($scenario->getCategories());

	config(['snapshot-testing.scenarios.time_travelers' => [
		Scenarios\Examples\TodayIsMarch3rd2021::class => [
			Snapshots\Examples\UsersHadNoUsername::class
		]
	]]);

	$scenario = SnapshotTesting::usingScenario(Scenarios\Examples\TodayIsMarch3rd2021::class);
	$snapshots = $scenario->getCategories();

	assertNotEmpty($snapshots);
	assertCount(1, $snapshots);
	assertSame(Snapshots\Examples\UsersHadNoUsername::class, get_class(head($snapshots)));
});

it('time travels using a time travel scenario', function () {
	SnapshotTesting::usingScenario(Scenarios\Examples\TodayIsMarch3rd2021::class);

	$this->assertSame(
		'2021-03-03',
		now()->toDateString(),
	);
});
