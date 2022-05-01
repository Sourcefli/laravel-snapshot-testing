<?php

use Mockery as m;
use Mockery\MockInterface;
use Sourcefli\SnapshotTesting\Contracts\IDatabaseSnapshot;
use Sourcefli\SnapshotTesting\Contracts\IScenario;
use Sourcefli\SnapshotTesting\Contracts\ITimeTravelScenario;
use Sourcefli\SnapshotTesting\Facades\SnapshotTesting;
use Sourcefli\SnapshotTesting\Scenarios;
use Sourcefli\SnapshotTesting\Snapshots;
use function PHPUnit\Framework\assertContainsOnlyInstancesOf;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertNotEmpty;

it('knows its owned categories')
	->expect(fn () => Scenarios\Examples\TodayIsMarch3rd2021::make()->getCategories())
	->toBe([ITimeTravelScenario::CATEGORY]);


it('can declare its own database snapshots for each scenario', function () {
	/** @var Scenarios\Examples\TodayIsMarch3rd2021 $scenario */
	$scenario = m::mock(Scenarios\Examples\TodayIsMarch3rd2021::class, IScenario::class, function (MockInterface $mock) {
		$mock
			->makePartial()
			->allows([
				'setupTestEnvironment' => null,
				'snapshotDeclarations' => [
					Snapshots\Examples\UsersHaveNoUsername::class
				]
		]);
	});


	SnapshotTesting::usingScenario($scenario);

	# should return the combined snapshots:
	# 	1. any listed in the config
	# 	2. any listed in the class' snapshotDeclarations() method
	$snapshots = SnapshotTesting::getSnapshotsForCategory(ITimeTravelScenario::class);

	$expectedSnapshots = [Snapshots\Examples\UsersHaveNoUsername::class];
	if (! empty($configuredSnapshots = snapshotPackageConfig('scenarios.time_traveler_scenarios'))) {
		$expectedSnapshots[] = $configuredSnapshots[Scenarios\Examples\TodayIsMarch3rd2021::class];
	}

	assertNotEmpty($snapshots);
	assertCount(count($expectedSnapshots), $snapshots);
	assertContainsOnlyInstancesOf(IDatabaseSnapshot::class, $snapshots);
});
