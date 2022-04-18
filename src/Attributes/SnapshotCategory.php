<?php

namespace Sourcefli\SnapshotTesting\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class SnapshotCategory
{
	public function __construct(
	    protected string $class,
		protected ?string $category = null
	) {}

	public function getCategory(): ?string
	{
		return $this->category;
	}

	public function getClass(): string
	{
		return $this->class;
	}
}
