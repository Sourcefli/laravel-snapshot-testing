<?php

use Illuminate\Contracts\Filesystem\Filesystem;
use Mockery as m;
use Mockery\MockInterface;
use Sourcefli\SnapshotTesting\Contracts;
use Sourcefli\SnapshotTesting\Contracts\IBasicScenario;
use Sourcefli\SnapshotTesting\Contracts\ITimeTravelScenario;
use Sourcefli\SnapshotTesting\Facades\SnapshotTesting;
use Sourcefli\SnapshotTesting\Scenarios;
use Sourcefli\SnapshotTesting\Scenarios\Examples\TodayIsMarch3rd2021;
use Sourcefli\SnapshotTesting\Snapshots;
use Sourcefli\SnapshotTesting\Snapshots\Examples\UsersHaveNoUsername;
use Sourcefli\SnapshotTesting\Snapshots\Examples\UsersHaveOnePostPerMonth;
use function Pest\Laravel\swap;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

it('knows valid categories')
	->expect(fn () => SnapshotTesting::getAvailableCategories()->all())
	->toBe([
		IBasicScenario::CATEGORY,
		ITimeTravelScenario::CATEGORY,
	]);

it('has time travel scenarios when configured', function () {
	$scenarios = SnapshotTesting::getTimeTravelScenarios();

	$this->assertCount(2, $scenarios);

	foreach ($scenarios as $scenario) {
		$this->assertContains(Contracts\ITimeTravelScenario::class, class_implements($scenario));
	}
});

it('has a snapshot connection', function () {
	$snapshotConnection = SnapshotTesting::getConnection();

	$this->assertContains(Contracts\ISnapshotConnection::class, class_implements($snapshotConnection));
});

it('has a disk', function () {
	$disk = SnapshotTesting::getDisk();

	$this->assertContains(Filesystem::class, class_implements($disk));
	$this->assertStringEndsWith('storage/framework/cache/snapshots', $disk->getConfig()['root']);
});

it('provides scenario contracts that are currently available', function () {
	$currentlyAvailable = [
		Contracts\IBasicScenario::class,
		Contracts\ITimeTravelScenario::class
	];

	$contracts = SnapshotTesting::collectCategorizedContractInfo();

	$this->assertSame($currentlyAvailable, $contracts->flatMap->pluck('class')->all());
});

it('registers new scenarios as theyre being used', function () {
	assertEmpty(SnapshotTesting::getScenarios());

	SnapshotTesting::usingScenario($scenario = TodayIsMarch3rd2021::class);

	assertTrue(SnapshotTesting::hasUsedScenario($scenario));
});

it('can declare snapshots in config at runtime', function () {
	$category = Contracts\ITimeTravelScenario::CATEGORY;

	# Start with no snapshots configured
	config(['snapshot-testing.scenarios' => [
		IBasicScenario::CATEGORY => [],
		ITimeTravelScenario::CATEGORY => [],
	]]);


	assertEmpty(SnapshotTesting::getSnapshotsForCategory($category));

	# Fill em up!
	config(["snapshot-testing.scenarios.$category" => [
		Scenarios\Examples\TodayIsMarch3rd2021::class => [
			Snapshots\Examples\UsersHaveNoUsername::class,
			Snapshots\Examples\UsersHaveOnePostPerMonth::class
		]
	]]);

	assertNotEmpty($snapshots = SnapshotTesting::getSnapshotsForCategory($category)->all());
	assertCount(2, $snapshots);
	assertSame(Snapshots\Examples\UsersHaveNoUsername::class, get_class($snapshots[0]));
});

it('can declare snapshots in the class at runtime', function () {
	$category = Contracts\ITimeTravelScenario::CATEGORY;

	# Start with no snapshots configured
	config(['snapshot-testing.scenarios' => [
		IBasicScenario::CATEGORY => [],
		ITimeTravelScenario::CATEGORY => [],
	]]);

	assertEmpty(SnapshotTesting::getSnapshotsForCategory($category));

	# Add snapshots for next call that comes into this scenario
	swap(TodayIsMarch3rd2021::class, m::mock(TodayIsMarch3rd2021::class, Contracts\IScenario::class, function (MockInterface $mock) {
	    $mock->allows(['snapshotDeclarations' => [
			UsersHaveOnePostPerMonth::class,
			UsersHaveNoUsername::class
		]]);
		$mock->allows('setupTestEnvironment')->andReturnNull();
		$mock->allows('addCategory')->with($category = ITimeTravelScenario::CATEGORY);
		$mock->allows('getCategories')->andReturn([$category]);
	}));

	SnapshotTesting::usingScenario(app(TodayIsMarch3rd2021::class));

	assertNotEmpty($snapshots = SnapshotTesting::getSnapshotsForCategory($category)->all());
	assertCount(2, $snapshots);
	assertSame([
		UsersHaveOnePostPerMonth::class,
		UsersHaveNoUsername::class
	], array_map(fn ($c) => get_class($c), $snapshots));
});
