# Run PHPUnit tests against pre-determined database state using .sql or .sqlite files

More details coming soon!

## Installation

You can install the package via composer:

```bash
composer require sourcefli/laravel-snapshot-testing
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-snapshot-testing-config"
```

This is the contents of the published config file:

```php
return [
    # The disk used when checking for .sql or .sqlite files
    'disk' => [
        'snapshot-testing' => [
            'driver' => 'local',
            'root' => storage_path('framework/cache/snapshots'),
            'throw' => false,
        ],
    ],

    # The database used when refreshing/setting up database state using pre-defined snapshots
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

    # The varying scenarios you'd like to use. Each much fall under a pre-existing category (as listed here).
    # If you have any more category ideas, please let me know! 
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
```

## Usage

```php
// Coming soon
```

## Testing

Tests written in pest PHP.

```bash
composer test
```

## Contributing

PRs and any other contributions are welcome!

## Security Vulnerabilities

Please [email me](mailto:mail@jhavens.tech) if you find any security vulnerabilities

## Credits

- [Jonathan Havens](https://github.com/sourcefli)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
