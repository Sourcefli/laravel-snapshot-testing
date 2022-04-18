<?php

namespace Sourcefli\SnapshotTesting;

use Closure;
use Illuminate\Database\Connection as DBConnection;
use Illuminate\Database\Console\Migrations\FreshCommand;
use Illuminate\Database\Schema\Builder as SchemaBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JetBrains\PhpStorm\ArrayShape;
use Sourcefli\SnapshotTesting\Contracts\IDatabaseSnapshot;
use Sourcefli\SnapshotTesting\Contracts\ISnapshotConnection;
use Sourcefli\SnapshotTesting\Exceptions\SnapshotTestingException;

class SnapshotConnection implements ISnapshotConnection
{
	use HasSnapshotConfig;

	/**
	 * @var class-string<IDatabaseSnapshot>
	 */
	protected string $currentSnapshot;

	/**
	 * @var DBConnection
	 */
	protected DBConnection $dbConnection;

	/**
	 * @var Closure|bool
	 */
	protected static Closure|bool $shouldRefresh;

	/**
	 * @var SnapshotTesting
	 */
	protected SnapshotTesting $snapshotManager;

	public function __construct()
	{
		$this->snapshotManager = app('snapshot-testing');

		$this->setDatabase(
			$this->getConfiguredConnection()
		);

		static::shouldRefreshWhen(function (IDatabaseSnapshot $newSnapshot, ?IDatabaseSnapshot $previousSnapshot = null) {
		    return (bool) $this->getConfig('database.should_refresh_database_when_switching_scenarios', true);
		});
	}

	public function refreshDatabase(): void
	{
		$this->getSchema()->dropAllTables();

		Artisan::call(FreshCommand::class, [
			'--database' => $this->getDatabase()->getName(),
		]);
	}

	public function getSchema(): SchemaBuilder
	{
		return Schema::connection($this->getDatabase()->getName());
	}

	/** Keeps checking config so connection can be swapped out as needed */
	public function getDatabase(): DBConnection
	{
		$configuredConnection = $this->getConfiguredConnection();

		if ($configuredConnection->getDatabaseName() !== $this->dbConnection->getDatabaseName()) {
			DB::purge($this->dbConnection->getName());

			$this->setDatabase($configuredConnection);
		}

		return $this->dbConnection;
	}

	public function setDatabase(DBConnection $connection): static
	{
		$this->dbConnection = $connection;

		return $this;
	}

	/**
	 * $shouldRefresh Signature -> function(IDatabaseSnapshot $newSnapshot, ?IDatabaseSnapshot $previousSnapshot = null): bool
	 *
	 * @param  bool|Closure  $shouldRefresh
	 */
	public static function shouldRefreshWhen(bool|Closure $shouldRefresh): void
	{
		static::$shouldRefresh = $shouldRefresh;
	}

	/**
	 * @param  string|IDatabaseSnapshot  $newSnapshot
	 *
	 * @return static
	 */
	public function setCurrentSnapshot(string|IDatabaseSnapshot $newSnapshot): static
	{
		$this->currentSnapshot = $newSnapshot;

		if (isset(static::$shouldRefresh) && ! value(static::$shouldRefresh, $newSnapshot, $this->currentSnapshot ?? null)) {
			return $this;
		}

		$this->refreshDatabase();

		return $this;
	}

	private function getConfiguredConnection(): DBConnection
	{
		$settings = $this->getConfig('database.connection');

		if (is_string($settings)) {
			return DB::connection($settings);
		}

		if (! is_array($settings)) {
			throw SnapshotTestingException::invalidConfiguration(
				gettype($settings),
				"snapshot.connection configuration must be a string (existing connection name), or an array containing connection settings"
			);
		}

		$settings = $settings + self::getInMemorySettings();

		config()->set("database.connections.".$name = Arr::pull($settings, 'name'), $settings);

		return DB::connection($name);
	}

	#[ArrayShape([
		'name' => "string",
		'driver' => "string",
		'database' => "string",
		'prefix' => "string",
		'foreign_key_constraints' => "bool"
	])]
	public static function getInMemorySettings(): array
	{
		return [
			'name' => 'snapshot_testing_connection',
			'driver' => 'sqlite',
			'database' => ':memory:',
			'prefix' => '',
			'foreign_key_constraints' => true,
		];
	}
}
