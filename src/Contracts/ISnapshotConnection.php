<?php

namespace Sourcefli\SnapshotTesting\Contracts;

use Closure;
use Illuminate\Database\Connection as DBConnection;
use Illuminate\Database\Schema\Builder as SchemaBuilder;

interface ISnapshotConnection
{
	/**
	 * @return DBConnection
	 */
	public function getDatabase(): DBConnection;

	/**
	 * @return SchemaBuilder
	 */
	public function getSchema(): SchemaBuilder;

	/**
	 * @param  DBConnection  $connection
	 *
	 * @return static
	 */
	public function setDatabase(DBConnection $connection): static;

	/**
	 * @return void
	 */
	public function refreshDatabase(): void;

	/**
	 * @param  string|IDatabaseSnapshot  $newSnapshot
	 *
	 * @return static
	 */
	public function setCurrentSnapshot(string|IDatabaseSnapshot $newSnapshot): static;

	/**
	 * @param  bool|Closure  $shouldRefresh
	 */
	public static function shouldRefreshWhen(bool|Closure $shouldRefresh): void;
}
