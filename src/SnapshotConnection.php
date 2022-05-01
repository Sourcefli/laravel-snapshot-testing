<?php

namespace Sourcefli\SnapshotTesting;

use Closure;
use Illuminate\Database\Connection as DBConnection;
use Illuminate\Database\Console\Migrations\FreshCommand;
use Illuminate\Database\Schema\Builder as SchemaBuilder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
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

	public function __construct()
	{
		$this->setDatabase(
			$this->getConfiguredConnection()
		);

		static::$shouldRefresh ??= function (IDatabaseSnapshot $newSnapshot, ?IDatabaseSnapshot $previousSnapshot = null) {
			return (bool) $this->getSnapshotConfig('database.refresh_database_when_switching_scenarios', true);
		};
	}

	public function refreshDatabase(): void
	{
		$this->getSchema()->dropAllTables();

		Artisan::call(FreshCommand::class, [
			'--database' => $this->getDatabaseConnection()->getName(),
		]);
	}

	public function getSchema(): SchemaBuilder
	{
		return Schema::connection($this->getDatabaseConnection()->getName());
	}

	public function getDatabaseConnection(): DBConnection
	{
		$configuredConnection = $this->getConfiguredConnection();

		if ($configuredConnection->getName() !== $this->dbConnection->getName()) {
			$this->setDatabase($configuredConnection);
		}

		return $this->dbConnection;
	}

	public function setDatabase(DBConnection|string $connection): static
	{
		$this->dbConnection = is_string($connection) ? \DB::connection($connection) : $connection;

		return $this;
	}

	/**
	 * Signature: function(IDatabaseSnapshot $newSnapshot, ?IDatabaseSnapshot $previousSnapshot = null): bool
	 */
	public static function shouldRefreshWhen(bool|Closure $shouldRefresh): void
	{
		static::$shouldRefresh = $shouldRefresh;
	}

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
		$connectionName = $this->getSnapshotConfig('database.connection');

		if (! is_string($connectionName)) {
			throw SnapshotTestingException::invalidConfiguration(
				gettype($connectionName), 'an existing connection name'
			);
		}

		try {
			return DB::connection($connectionName);
		} catch (InvalidArgumentException $e) {
		    if (str_ends_with($e->getMessage(), "[$connectionName] not configured.")) {
				throw SnapshotTestingException::invalidConfiguration(
					$connectionName,
					"an existing connection name"
				);
			}

			throw $e;
		}
	}
}
