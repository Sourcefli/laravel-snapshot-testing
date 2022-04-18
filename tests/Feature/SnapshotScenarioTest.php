<?php

use Mockery as m;
use Mockery\MockInterface;
use Sourcefli\SnapshotTesting\Contracts\IScenario;
use Sourcefli\SnapshotTesting\Contracts\ITimeTravelScenario;
use Sourcefli\SnapshotTesting\Facades\SnapshotTesting;
use Sourcefli\SnapshotTesting\Scenarios;
use Sourcefli\SnapshotTesting\Snapshots;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertSame;

it('knows its owned categories')
	->expect(fn () => app(Scenarios\Examples\TodayIsMarch3rd2021::class)->collectOwnedCategories()->all())
	->toBe([ITimeTravelScenario::CATEGORY]);


it('can declare its own database snapshots for each scenario', function () {
	$scenario = m::mock(Scenarios\Examples\TodayIsMarch3rd2021::class, IScenario::class, function (MockInterface $mock) {
		$mock
			->makePartial()
			->allows([
				'setupTestEnvironment' => null,
				'getSnapshots' => [
					Snapshots\Examples\UsersHaveNoUsername::class
				]
		]);
	});

	$scenario->setSnapshotManager(app('snapshot-testing'));

	$snapshots = SnapshotTesting::usingScenario($scenario)->getCategories();

	assertNotEmpty($snapshots);
	assertCount(1, $snapshots);
	assertSame(Snapshots\Examples\UsersHaveNoUsername::class, head($snapshots));
});
