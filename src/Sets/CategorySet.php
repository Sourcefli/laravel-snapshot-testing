<?php

namespace Sourcefli\SnapshotTesting\Sets;

use Ramsey\Collection\Set;
use Sourcefli\SnapshotTesting\Contracts\IScenario;

class CategorySet extends Set
{
	protected function __construct(array $data = [])
	{
		parent::__construct(IScenario::class, $data);
	}

	public static function make(iterable $data): static
	{
		return new static(collect($data)->all());
	}
}
