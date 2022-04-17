<?php

namespace Sourcefli\SnapshotTesting;


use Carbon\CarbonInterface;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Sourcefli\SnapshotTesting\Scenarios\SnapshotScenario;
use Sourcefli\SnapshotTesting\Scenarios\TimeTravel\ITimeTravelScenario;
use Sourcefli\SnapshotTesting\Snapshots\Connection as SnapshotConnection;

class SnapshotTesting
{
	use HasSnapshotConfig;

	/**
	 * @var ITimeTravelScenario[]
	 */
	protected array $timeTravelScenarios = [];

	public function __construct(
		protected Repository $config
	) {}

	public function getConnection(): SnapshotConnection
	{
		return app(SnapshotConnection::class);
	}

	public function getDisk(): Filesystem
	{
		return Storage::disk('snapshot-testing');
	}

	/**
	 * @return ITimeTravelScenario[]
	 */
	public function getTimeTravelScenarios(): array
	{
		return $this->getConfig('scenarios.time_travelers');
	}

	public function whenCurrentDateIs(CarbonInterface $currentDate): ITimeTravelScenario&SnapshotScenario
	{

	}
}
