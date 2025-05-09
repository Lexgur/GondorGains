<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Script;

use Lexgur\GondorGains\ClassFinder;
use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Service\SeederDependencyResolver;
use ReflectionException;
use Throwable;

class RunSeedersScript implements ScriptInterface
{
    private string $seedersDirectory;

    private string $seededRegistryPath;

    private Container $container;

    public function __construct(string $seedersDirectory, string $seededRegistryPath, Container $container)
    {
        $this->seedersDirectory = $seedersDirectory;
        $this->seededRegistryPath = $seededRegistryPath;
        $this->container = $container;
    }

    /**
     * @return int
     * @throws ReflectionException
     * @throws Throwable
     */
    public function run(): int
    {
        $classFinder = new ClassFinder($this->seedersDirectory);
        $seederClasses = $classFinder->findClassesImplementing(SeederInterface::class);
        $successfulSeeders = $this->getSeededSeeders();

        $pendingSeederClasses = array_diff($seederClasses, $successfulSeeders);

        if (empty($pendingSeederClasses)) {
            echo "No pending seeders found." . PHP_EOL;
            return 1;
        }
        $sortedSeedersClasses = (new SeederDependencyResolver())->sortSeeders($pendingSeederClasses);

        foreach ($sortedSeedersClasses as $class) {
            /** @var SeederInterface $seeder */
            $seeder = $this->container->get($class);
            $seeder->seed();
            echo $class . PHP_EOL;

            $successfulSeeders[] = $class;
            file_put_contents($this->getSeededRegistryPath(), json_encode($successfulSeeders, JSON_PRETTY_PRINT));
        }

        return 0;
    }

    /** @return array<string> */
    private function getSeededSeeders(): array
    {
        $path = $this->getSeededRegistryPath();

        if (!file_exists($path)) {
            return [];
        }

        $content = file_get_contents($path);

        if ($content === false || trim($content) === '') {
            return [];
        }

        $decoded = json_decode($content, true);

        if (!is_array($decoded)) {
            return [];
        }

        return $decoded;
    }

    private function getSeededRegistryPath(): string
    {
        return $this->seededRegistryPath;
    }
}