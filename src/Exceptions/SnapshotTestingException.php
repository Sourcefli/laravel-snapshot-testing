<?php

namespace Sourcefli\SnapshotTesting\Exceptions;

use InvalidArgumentException;
use Sourcefli\SnapshotTesting\Facades\SnapshotTesting;

class SnapshotTestingException extends InvalidArgumentException
{
	protected const CONFIGURATION_EXCEPTION_CODE = 1008;

	public static function invalidConfiguration(string $received, string $expected): static
	{
		$message = sprintf("Invalid snapshot configuration, expected [%s], but received [%s]", $expected, $received);

		return new static($message, self::CONFIGURATION_EXCEPTION_CODE);
	}

	public static function unknownScenario(string $scenario): static
	{
		return new static(
			sprintf("unknown scenario type [%s], does this class implement one of the following snapshot scenario contracts: [%s]?",
				class_basename($scenario),
				SnapshotTesting::collectScenarioContracts()->values()->implode(', ')
			)
		);
	}
}
