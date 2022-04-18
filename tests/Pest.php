<?php

use Mockery as m;
use Sourcefli\SnapshotTesting\Tests\TestCase;

uses(TestCase::class)
	->afterAll(fn () => m::close())
	->in(__DIR__);
