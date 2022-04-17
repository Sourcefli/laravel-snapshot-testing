<?php

namespace Sourcefli\SnapshotTesting\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Sourcefli\SnapshotTesting\SnapshotTesting
 */
class SnapshotTesting extends Facade
{
    protected static function getFacadeAccessor(): string
	{
        return SnapshotTesting::class;
    }
}
