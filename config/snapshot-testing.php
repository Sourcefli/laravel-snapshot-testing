<?php

return [
	'disk' => [
		'snapshot-testing' => [
			'driver' => 'local',
			'root' => storage_path('framework/cache/snapshots'),
			'throw' => false,
		],
	],

	'database' => [
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
		'time_travelers' => [
			// add time traveler scenarios here...
		]
	]
];
