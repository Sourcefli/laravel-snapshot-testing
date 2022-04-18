<?php

namespace Sourcefli\SnapshotTesting\Contracts;

use Sourcefli\SnapshotTesting\Attributes\SnapshotCategory;

#[SnapshotCategory(self::class, self::CATEGORY)]
interface IBasicScenario extends IScenario
{
	const CATEGORY = 'basic_scenarios';
}
