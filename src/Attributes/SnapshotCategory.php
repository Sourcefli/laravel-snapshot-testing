<?php

namespace Sourcefli\SnapshotTesting\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class SnapshotCategory
{
	public function __construct(
	    protected string $class
	) {}
}
