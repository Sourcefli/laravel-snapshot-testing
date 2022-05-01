<?php

//it('knows valid categories', function () {
//	$collection = \Sourcefli\SnapshotTesting\Collections\CategoryCollection::make();
//
//	dd($collection);
////	\PHPUnit\Framework\assertSame();
//});
use Sourcefli\SnapshotTesting\Collections\SnapshotCategoriesCollection;
use Sourcefli\SnapshotTesting\Collections\SnapshotCollection;
use Sourcefli\SnapshotTesting\Contracts\ITimeTravelScenario;
use Sourcefli\SnapshotTesting\Scenarios\Examples\TodayIsMarch3rd2021;
use Sourcefli\SnapshotTesting\Snapshots\Examples\UsersHaveManyPostsPerMonth;
use Sourcefli\SnapshotTesting\Snapshots\Examples\UsersHaveNoUsername;
use Sourcefli\SnapshotTesting\Snapshots\Examples\UsersHaveOnePostPerMonth;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

it('can add new categories', function () {
    $collection = SnapshotCategoriesCollection::make();

	$snapshots = SnapshotCollection::make([
		app(UsersHaveManyPostsPerMonth::class),
		app(UsersHaveNoUsername::class),
	])->setScenario(TodayIsMarch3rd2021::class);

	$collection->addSnapshots($snapshots);

	assertTrue($collection->hasScenario(TodayIsMarch3rd2021::class));
	assertCount(2, $collection->getCategory(ITimeTravelScenario::CATEGORY));
});


it('wont duplicate snapshots for the same scenario', function () {
	$snapshotCategories = SnapshotCategoriesCollection::make()->addSnapshots(
		$snapshots = SnapshotCollection::make(
			[
				app(UsersHaveOnePostPerMonth::class),
				app(UsersHaveNoUsername::class),
			]
		)->setScenario($scenario = TodayIsMarch3rd2021::class)
	);

	# try adding duplicates...
	$snapshotCategories->addSnapshots(
		SnapshotCollection::make([app(UsersHaveOnePostPerMonth::class)])->setScenario($scenario)
	);

	$snapshotResults = $snapshotCategories->forScenario($scenario);

	assertSame($snapshots, $snapshotResults);
	assertCount(2, $snapshotResults);
});
