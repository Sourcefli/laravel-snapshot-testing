<?php

namespace Sourcefli\SnapshotTesting\Exceptions;

use InvalidArgumentException;
use Sourcefli\SnapshotTesting\Contracts\IDatabaseSnapshot;
use Sourcefli\SnapshotTesting\Contracts\IScenario;
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
				self::getScenarioContracts()
			)
		);
	}

	public static function snapshotNotFound(string|IDatabaseSnapshot $databaseSnapshot, string|IScenario $scenario): static
	{
		$snapshot = class_basename($databaseSnapshot);
		$scenario = class_basename($scenario);

		return new static(
			"[{$scenario}] does not support snapshot [{$snapshot}]. To add new snapshots, list them in the snapshotDeclarations() method of each scenario"
		);
	}

	public static function categoryNotFound(string $snapshotClass)
	{
		$snapshotClass = class_basename($snapshotClass);

		return new static(
			"no snapshot category was found for [{$snapshotClass}]. This class must implement one of the built-in scenario-based contracts: [".self::getScenarioContracts()."]"
		);
	}

	private static function getScenarioContracts(): string
	{
		return SnapshotTesting::collectCategorizedContractInfo()->flatMap->pluck('class')->implode(', ');
	}

	public static function classInvalid(string|object $invalidClass, string $expectedClass): static
	{
		$invalidClass = class_basename($invalidClass);
		$expectedClass = class_basename($expectedClass);

		return new static(
			"Expected class of type [{$expectedClass}], received [{$invalidClass}]"
		);
	}

	public static function vendorError(string $message): static
	{
		return new static("Vendor error: {$message}");
	}

	public static function invalidSnapshotCategory(string $categories): static
	{
		return new static("Invalid snapshot category/categories provided: [" .$categories. "]");
	}
}
