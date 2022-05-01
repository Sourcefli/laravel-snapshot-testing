<?php

namespace Sourcefli\SnapshotTesting\Scenarios\Examples;

use Sourcefli\SnapshotTesting\Contracts\IBasicScenario;
use Sourcefli\SnapshotTesting\Scenarios\SnapshotScenario;

class NoSetupRequired extends SnapshotScenario implements IBasicScenario
{
	public function setup(): void
	{
		//
	}
}
