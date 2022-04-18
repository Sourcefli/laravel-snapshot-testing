<?php

//it('knows valid categories', function () {
//	$collection = \Sourcefli\SnapshotTesting\Collections\CategoryCollection::make();
//
//	dd($collection);
////	\PHPUnit\Framework\assertSame();
//});

use Sourcefli\SnapshotTesting\Collections\CategoryCollection;
use Sourcefli\SnapshotTesting\Collections\SnapshotCollection;
use Sourcefli\SnapshotTesting\Contracts\ITimeTravelScenario;
use Sourcefli\SnapshotTesting\Scenarios\Examples\TodayIsMarch3rd2021;
use Sourcefli\SnapshotTesting\Snapshots\Examples\UsersHaveManyPostsPerMonth;
use Sourcefli\SnapshotTesting\Snapshots\Examples\UsersHaveNoUsername;
use Sourcefli\SnapshotTesting\Snapshots\Examples\UsersHaveOnePostPerMonth;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

it('can add new categories', function () {
    $collection = CategoryCollection::make();

	$snapshots = SnapshotCollection::make([
		app(UsersHaveManyPostsPerMonth::class),
		app(UsersHaveNoUsername::class),
	])->setScenario(TodayIsMarch3rd2021::class);

	$collection->addSnapshotCollection($snapshots);

	assertTrue($collection->hasScenario(TodayIsMarch3rd2021::class));
	assertCount(2, $collection->getCategory(ITimeTravelScenario::CATEGORY));
});

it('wont duplicate snapshots of the same scenario', function () {
    $collection = CategoryCollection::make();

	$snapshots1 = SnapshotCollection::make([
		app(UsersHaveOnePostPerMonth::class),
		app(UsersHaveNoUsername::class),
	])->setScenario($scenario = TodayIsMarch3rd2021::class);

	$collection->addSnapshotCollection($snapshots1);

	$duplicateSnapshots1 = SnapshotCollection::make([
		app(UsersHaveNoUsername::class),
	])->setScenario($scenario);

	$collection->addSnapshotCollection($duplicateSnapshots1);

	assertCount(2, $collection->findByScenario($scenario));

	# Has one new one
	$collection->addSnapshotCollection(
		SnapshotCollection::make([
			app(UsersHaveManyPostsPerMonth::class),
			app(UsersHaveNoUsername::class),
		])->setScenario($scenario)
	);

	assertCount(3, $finalSnapshots = $collection->findByScenario($scenario));
	assertSame([
		UsersHaveOnePostPerMonth::class,
		UsersHaveNoUsername::class,
		UsersHaveManyPostsPerMonth::class
	], collect($finalSnapshots)->map(fn ($c) => $c::class)->all());
});

it('wont duplicate scenario if it already exists', function () {
    $collection = CategoryCollection::make();

	$snapshots1 = SnapshotCollection::make([
		app(UsersHaveOnePostPerMonth::class),
		app(UsersHaveNoUsername::class),
	])->setScenario($scenario = TodayIsMarch3rd2021::class);

	$collection->addSnapshotCollection($snapshots1);

	assertInstanceOf(SnapshotCollection::class, $snapshots2 = $collection->findByScenario($scenario));
	assertSame($snapshots1, $snapshots2);

	$snapshots3 = SnapshotCollection::make([
		app(UsersHaveManyPostsPerMonth::class),
	])->setScenario($scenario);

	$collection->addSnapshotCollection($snapshots3);

	assertSame($snapshots1, $snapshots4 = $collection->findByScenario($scenario));
	assertCount(3, $snapshots4);
});
