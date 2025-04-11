<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Script;

use Lexgur\GondorGains\ClassFinder;
use Lexgur\GondorGains\Container;

class RunMigrationsScript implements ScriptInterface
{

    protected ClassFinder $classFinder;

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
        $this->classFinder = new ClassFinder($this->directory);
        $migrationClasses = $this->classFinder->findClassesImplementing(MigrationInterface::class);

        $migrations = [];
        $successfulMigrations = $this->getMigratedMigrations();

        $pendingMigrations = array_diff($migrationClasses, $successfulMigrations);

        if (empty($pendingMigrations)) {
            echo "No pending migrations found." . PHP_EOL;
            return 1;
        }

        foreach ($migrationClasses as $migrationClass) {
            $migration = $this->container->get($migrationClass);
            $migrations[] = $migration;
        }

        usort($migrations, function (MigrationInterface $a, MigrationInterface $b) {
            return $a->order() <=> $b->order();
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

    private function getMigratedMigrations(): array
    {
        if (!file_exists($this->getMigratedRegistryPath())) {
            return [];
        }
        $migratedMigrations[] = json_decode(file_get_contents($this->getMigratedRegistryPath()), true);

        return $migratedMigrations;
    }
}