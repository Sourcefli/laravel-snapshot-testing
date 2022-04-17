<?php

namespace Sourcefli\SnapshotTesting\Snapshots;

use Database\Seeders\UserSeeder;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Connection as DBConnection;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Console\Migrations\FreshCommand;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sourcefli\SnapshotTesting\HasSnapshotConfig;
use Sourcefli\SnapshotTesting\SnapshotTesting;

class Connection
{
	use HasSnapshotConfig;

	/**
	 * @var class-string<IDatabaseSnapshot>
	 */
	protected string $currentSnapshot;

	public function __construct(
		protected Repository $config,
		protected SnapshotTesting $snapshotManager
	) {}

	/**
	 * @return static
	 */
	public function refreshDatabase(): static
	{
		$this->migrateDatabase();

		$tableCounts = [
			Approval::TABLE => Approval::count(),
			LoggedExecution::TABLE => LoggedExecution::count(),
			Execution::TABLE => Execution::count(),
			QueueJob::TABLE => QueueJob::count(),
			'failed_jobs' => $this->connection->table('failed_jobs')->count(),
			'job_batches' => $this->connection->table('job_batches')->count(),
		];

		$this->getSchema()->disableForeignKeyConstraints();

		foreach ($tableCounts as $table => $count) {
			if (! $count) {
				continue;
			}

			$this->connection->table($table)->truncate();
		}

		$this->getSchema()->enableForeignKeyConstraints();

		return $this;
	}

	public function getSchema()
	{
		return Schema::connection($this->getDatabaseConnection()->getName());
	}

	public function getDatabaseConnection(): ConnectionInterface|DBConnection
	{
		return DB::connection($this->getConfig('connection'));
	}

	/**
	 * @return void
	 */
	private function migrateDatabase()
	{
		if ($this->snapshotManager->currentSnapshot)
		if ($this->usesSQLiteConnection && ! $this->connection instanceof SQLiteConnection) {
			return;
		}

		$existingTables = $existingTables ?? method_exists($this->connection->query(), 'getTables') ? $this->connection->query()->getTables() : throw new RuntimeException('Unable to determine ');

		if (method_exists($this->connection->query(), 'getTables') &&
			$this->connection->query()->getTables()->isNotEmpty()) {
			return;
		}

		Artisan::call(FreshCommand::class, [
			'--database' => $this->connection->getName(),
			'--seed' => true,
			'--seeder' => UserSeeder::class
		]);
	}
}
