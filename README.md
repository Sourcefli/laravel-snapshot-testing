# Run PHPUnit tests against pre-existing (known) database state using .sql or .sqlite files

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
<?php

use Sourcefli\SnapshotTesting\Contracts\IBasicScenario;
use Sourcefli\SnapshotTesting\Contracts\ITimeTravelScenario;
use Sourcefli\SnapshotTesting\Scenarios;
use Sourcefli\SnapshotTesting\Snapshots;

return [
    # Where your .sql/.sqlite files are stored
    'disk' => [
        'snapshot-testing' => [
            'driver' => 'local',
            'root' => storage_path('framework/cache/snapshots'),
            'throw' => false,
        ],
    ],

    # The database connection used
	'database' => [
		'connection' => 'testing',
		'refresh_database_when_switching_scenarios' => true
	],

    # The varying scenarios you'd like to use. Each much fall under a pre-existing category (as listed here).
    # If you have any more category ideas, please let me know! 
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
