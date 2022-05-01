<?php

use Sourcefli\SnapshotTesting\Contracts\IBasicScenario;
use Sourcefli\SnapshotTesting\Contracts\ITimeTravelScenario;
use Sourcefli\SnapshotTesting\Scenarios;
use Sourcefli\SnapshotTesting\Snapshots;

return [

	'disk' => [
		'snapshot-testing' => [
			'driver' => 'local',
			'root' => storage_path('framework/cache/snapshots'),
			'throw' => false,
		],
	],

	'database' => [
		'connection' => env('SNAPSHOT_CONNECTION'),
		'refresh_database_when_switching_scenarios' => true
	],

	'scenarios' => [
		IBasicScenario::CATEGORY => [
			Scenarios\Examples\NoSetupRequired::class => [
				Snapshots\Examples\UsersHaveOnePostPerMonth::class,
			],
		],
		ITimeTravelScenario::CATEGORY => [
			// Add time traveler scenarios here, a couple examples have been provided
			Scenarios\Examples\TodayIsMarch3rd2021::class => [
				Snapshots\Examples\UsersHaveOnePostPerMonth::class,
			],
			Scenarios\Examples\TodayIsApril1st2022::class => [
				// {@see \Sourcefli\SnapshotTesting\Scenarios\Examples\TodayIsApril1st2022::snapshotDeclarations()}
				// for alternative option when declaring snapshots
			],
		]
	]
];
