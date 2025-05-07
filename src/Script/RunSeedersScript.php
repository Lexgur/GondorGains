<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Script;

use Lexgur\GondorGains\ClassFinder;
use Lexgur\GondorGains\Container;

class RunSeedersScript implements ScriptInterface
{
    private string $directory;

    private string $seededRegistryPath;

    private Container $container;

    public function __construct(string $directory, string $seededRegistryPath, Container $container)
    {
        $this->directory = $directory;
        $this->seededRegistryPath = $seededRegistryPath;
        $this->container = $container;
    }

    /**
     * @return int
     * @throws \ReflectionException
     * @throws \Throwable
     */
    public function run(): int
    {
        $classFinder = new ClassFinder($this->directory);
        $seederClasses = $classFinder->findClassesImplementing(SeederInterface::class);

        $seeders = [];
        $successfulSeeders = $this->getSeededSeeders();

        $pendingSeeders = array_diff($seederClasses, $successfulSeeders);

        if (empty($pendingSeeders)) {
            echo "No pending seeders found." . PHP_EOL;
            return 1;
        }

        foreach ($pendingSeeders as $pendingSeeder) {
            $seeder = $this->container->get($pendingSeeder);
            $seeders[] = $seeder;
        }

        usort($seeders, function (SeederInterface $seederA, SeederInterface $seederB) {
            return $seederA->order() <=> $seederB->order();
        });

        foreach ($seeders as $seeder) {
            $seeder->seed();
            $successfulSeeders[] = $seeder::class;
            file_put_contents($this->getSeededRegistryPath(), json_encode($successfulSeeders));
        }

        return 0;
    }

    private function getSeededRegistryPath(): string
    {
        return $this->seededRegistryPath;
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
}
