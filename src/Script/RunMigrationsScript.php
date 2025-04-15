<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Script;

use Lexgur\GondorGains\ClassFinder;
use Lexgur\GondorGains\Container;

class RunMigrationsScript implements ScriptInterface
{

    private string $directory;

    private string $migratedRegistryPath;

    private Container $container;

    public function __construct(string $directory, string $migratedRegistryPath, Container $container)
    {
        $this->directory = $directory;
        $this->migratedRegistryPath = $migratedRegistryPath;
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
        $migrationClasses = $classFinder->findClassesImplementing(MigrationInterface::class);

        $migrations = [];
        $successfulMigrations = $this->getMigratedMigrations();

        $pendingMigrations = array_diff($migrationClasses, $successfulMigrations);

        if (empty($pendingMigrations)) {
            echo "No pending migrations found." . PHP_EOL;
            return 1;
        }

        foreach ($pendingMigrations as $pendingMigration) {
            $migration = $this->container->get($pendingMigration);
            $migrations[] = $migration;
        }

        usort($migrations, function (MigrationInterface $returnValueA, MigrationInterface $returnValueB) {
            return $returnValueA->order() <=> $returnValueB->order();
        });

        foreach ($migrations as $migration) {
            $migration->migrate();
            $successfulMigrations[] = $migration::class;
            file_put_contents($this->getMigratedRegistryPath(), json_encode($successfulMigrations));
        }

        return 0;
    }

    private function getMigratedRegistryPath(): string
    {
        return $this->migratedRegistryPath;
    }

    /** @return array<string> */
    private function getMigratedMigrations(): array
    {
        $path = $this->getMigratedRegistryPath();

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