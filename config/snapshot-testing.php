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
		'is_default_testing_connection' => true,
		'connection' => [
			'name' => 'snapshot_testing_connection',
			'driver' => 'sqlite',
			'url' => env('SNAPSHOT_DATABASE_URL'),
			'database' => env('SNAPSHOT_SQLITEDATABASE', ':memory:'),
			'prefix' => '',
			'foreign_key_constraints' => env('SNAPSHOT_DB_FOREIGN_KEYS', true),
		],
		'should_refresh_database_when_switching_scenarios' => true
	],

	'scenarios' => [
		IBasicScenario::CATEGORY => [

		],
		ITimeTravelScenario::CATEGORY => [
			// Add time traveler scenarios here, a couple examples have been provided
			Scenarios\Examples\TodayIsMarch3rd2021::class => [
				Snapshots\Examples\UsersHaveOnePostPerMonth::class,
			],
			Scenarios\Examples\TodayIsApril1st2022::class => [
				Snapshots\Examples\UsersHaveNoUsername::class,
				Snapshots\Examples\UsersHaveManyPostsPerMonth::class
			],
		]
	]
];
