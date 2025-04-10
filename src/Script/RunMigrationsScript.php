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

    public function __construct(string $directory, string $migratedRegistryPath)
    {
        $this->directory = $directory;
        $this->migratedRegistryPath = $migratedRegistryPath;
        $this->container = new Container();
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
        if (!file_exists($this->getMigratedRegistryPath())) {
            return [];
        }
        return json_decode(file_get_contents($this->getMigratedRegistryPath()), true);
    }
}