<?php

namespace Lexgur\GondorGains\Script;

use Lexgur\GondorGains\ClassFinder;

class RunMigrationsScript implements ScriptInterface{

    protected ClassFinder $classFinder;

    private string $directory;

    private string $migratedRegistryPath;

    public function __construct(string $directory, string $migratedRegistryPath)
    {
        $this->directory = $directory;
        $this->migratedRegistryPath = $migratedRegistryPath;
    }

    /**
     * @throws \ReflectionException
     */
    public function run(): int
    {
        $this->classFinder = new ClassFinder($this->directory);
        $this->getAllMigrations();

        return 0;
    }

    /**
     * @throws \ReflectionException
     */
    public function getAllMigrations(): array
    {
        $migrations = $this->classFinder->findClassesImplementing(MigrationInterface::class);

        return $migrations;
    }
}