<?php

namespace Sourcefli\SnapshotTesting\Exceptions;

use InvalidArgumentException;

class SnapshotTestingException extends InvalidArgumentException
{
	protected const CONFIGURATION_EXCEPTION_CODE = 1008;

	public static function invalidConfiguration(string $received, string $expected): static
	{
		$message = sprintf("Invalid snapshot configuration, expected [%s], but received [%s]", $expected, $received);

		return new static($message, self::CONFIGURATION_EXCEPTION_CODE);
	}
}
