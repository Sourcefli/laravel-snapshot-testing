<?php

namespace Sourcefli\SnapshotTesting;


use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use JetBrains\PhpStorm\ArrayShape;
use ReflectionAttribute;
use ReflectionClass;
use Sourcefli\SnapshotTesting\Attributes\SnapshotCategory;
use Sourcefli\SnapshotTesting\Collections\CategoryCollection;
use Sourcefli\SnapshotTesting\Collections\SnapshotCollection;
use Sourcefli\SnapshotTesting\Contracts\IScenario;
use Sourcefli\SnapshotTesting\Contracts\ISnapshotConnection;
use Sourcefli\SnapshotTesting\Exceptions\SnapshotTestingException;
use Sourcefli\SnapshotTesting\Scenarios\ScenarioStorage;
use Sourcefli\SnapshotTesting\Scenarios\SnapshotScenario;
use SplObjectStorage;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class SnapshotTesting
{
	use HasSnapshotConfig;

	/**
	 * @var ScenarioStorage<ScenarioStorage, CategoryCollection<array-key, SnapshotCollection>>
	 */
	protected ScenarioStorage $scenarios;

	/**
	 * @var CategoryCollection[]
	 */
	protected array $categories = [];

	public function __construct()
	{
	    $this->scenarios = new ScenarioStorage;
	}

	public function addScenario(SnapshotScenario $scenario): static
	{
		foreach ($this->getCategoriesForScenario($scenario) as $category) {
			$snapshots = $this->getSnapshotsForScenario($category, $scenario);

			if (! $this->scenarios->offsetExists($scenario)) {
				$scenario->addCategory($category);
				$this->scenarios[$scenario] = CategoryCollection::make([$category => $snapshots]);
				continue;
			}

			$this->scenarios->getCategoryCollection($scenario)->addSnapshotCollection($snapshots);
		}

		app()->instance(get_class($scenario), $scenario);

		return $this;
	}

	public function hasCategory(string $category): bool
	{
		return $this->getAvailableCategories()->contains($category);
	}

	/**
	 * @return Collection
	 */
	public function getAvailableCategories(): Collection
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->collectCategorizedContractInfo()->flatMap->pluck('category');
	}

	public function getCategoriesForScenario(string|IScenario $scenario): Collection
	{
		if (! is_a($scenario, IScenario::class, true)) {
			throw SnapshotTestingException::classInvalid($scenario, IScenario::class);
		}

		/** @var Collection $contractInfo */
		$contractInfo = $this->collectCategorizedContractInfo()->flatMap->pluck('class');

		return collect(class_implements($scenario))
			->intersect($contractInfo)
			->map(fn (string $scenarioContract) => new ReflectionClass($scenarioContract))
			->filter(fn (ReflectionClass $scenarioRflx) => count($scenarioRflx->getAttributes(SnapshotCategory::class)))
			->flatMap(fn (ReflectionClass $scenarioRflx) => array_map(
				fn (ReflectionAttribute $attribute) => $attribute->newInstance()->getCategory(), $scenarioRflx->getAttributes(SnapshotCategory::class))
			)
			->unique();
	}

	#[ArrayShape([
		'scenario' => IScenario::class,
		'categories' => CategoryCollection::class,
	])]
	public function getUsedScenarioWithInfo(string|IScenario $scenario): ?array
	{
		if (is_object($scenario)) {
			$scenario = $scenario::class;
		}

		foreach ($this->scenarios as $existingScenario) {
			if (get_class($existingScenario) === $scenario) {
				return ['scenario' => $scenario, 'categories' => $this->scenarios->getInfo()];
			}
		}

		return null;
	}

	public function getScenarios(): SplObjectStorage
	{
		return $this->scenarios;
	}

	public function getConnection(): ISnapshotConnection
	{
		return app(ISnapshotConnection::class);
	}

	public function getDisk(): Filesystem|FilesystemAdapter
	{
		return Storage::disk('snapshot-testing');
	}

	public function hasUsedScenario(string|IScenario $scenario): bool
	{
		if (is_object($scenario)) {
			$scenario = $scenario::class;
		}

		foreach ($this->scenarios as $existingScenario) {
			if (get_class($existingScenario) === $scenario) {
				return true;
			}
		}

		return false;
	}

	/** @return Collection<array-key, array<array-key, array{category: string, class: class-string}>> */
	public function collectCategorizedContractInfo(): Collection
	{
		$files = Finder::create()->in(__DIR__.DIRECTORY_SEPARATOR.'Contracts')->files()->getIterator();

		return collect($files)
			->map(fn (SplFileInfo $fileInfo) => new ReflectionClass("Sourcefli\\SnapshotTesting\\Contracts\\".$fileInfo->getFilenameWithoutExtension()))
			->filter(fn (ReflectionClass $class) => count($class->getAttributes(SnapshotCategory::class)))
			->map(fn (ReflectionClass $class) => defined(($contract = $class->getName()).'::CATEGORY')
				? [
					'category' => $contract::CATEGORY,
					'class' => $contract,
				]
				: throw SnapshotTestingException::vendorError("All IScenario contracts must have a CATEGORY constant defined. This is missing for {$contract}")
			)
			->groupBy('category');
	}

	public function usingScenario(string|IScenario $scenario): SnapshotScenario
	{
		/** @var IScenario&SnapshotScenario $newScenario */
		$newScenario = match(TRUE) {
			is_a($scenario, IScenario::class, true) => is_object($scenario) ? $scenario : app($scenario),
			default => throw SnapshotTestingException::unknownScenario($scenario)
		};

		$this->addScenario($newScenario);

		$newScenario->setupTestEnvironment();

		return $newScenario;
	}

	public function getSnapshotsForCategory(string $category): ?SnapshotCollection
	{
		collect($this->getConfig("scenarios.$category", []))
			->keys()
			->each(function (string $scenarioClass) use ($category) {
				# if we've already bound this scenario, make sure no updates were made to its snapshot(s)
				# if not, continue with the same instance (do nothing here)
				# is so, new up this scenario, so it gets registered
				if ($this->scenarioHasUpdatesForCategory($category, $scenarioClass)) {
					new $scenarioClass;
				}
			});


		# At this point, any previously missing scenarios/categories have been rebound
		# We can safely pull the scenario from this instance (if it exists and has snapshots configured)
		/** @var SnapshotScenario $scenario */
		foreach ($this->scenarios as $scenario) {
			$categoryCollection = $this->scenarios->getCategoryCollection($scenario);

			if ($categoryCollection->hasScenario($scenario)) {
				$categoryCollection->addSnapshotCollection(
					$this->getSnapshotsForScenario($category, $scenario)
				);

				return $categoryCollection->findByScenario($scenario);
			}
		}

		return null;
	}

	public function getSnapshotsForScenario($category, SnapshotScenario $scenario): SnapshotCollection
	{
		if (! $this->hasCategory($category)) {
			throw SnapshotTestingException::invalidSnapshotCategory($category);
		}

		return collect([
				...$this->hasUsedScenario($scenario) ? $this->getUsedScenarioWithInfo($scenario)['categories']?->findByScenario($scenario) : [],
				...$this->getConfiguredSnapshots($category),
				...$scenario->snapshotDeclarations(),
			])
			->map(fn (string|object $class) => is_object($class) ? get_class($class) : $class)
			->unique()
			->pipe(fn (Collection $snapshots) => SnapshotCollection::make($snapshots->all())->setScenario($scenario));
	}

	/** returns true if this scenario hasn't been used yet */
	public function scenarioHasUpdatesForCategory(string $category, string|SnapshotScenario $scenario): bool
	{
		if (! app()->bound(is_string($scenario) ? $scenario : get_class($scenario))) {
			return true;
		}

		$existingSnapshots = ($this->getUsedScenarioWithInfo($scenario)['categories'] ?? CategoryCollection::make())->findByScenario($scenario);
		$newSnapshots = $this->getSnapshotsForScenario($category, app($scenario));

		return $existingSnapshots->isEmpty() ||
			   count($newSnapshots) !== $existingSnapshots->count();
	}
}
