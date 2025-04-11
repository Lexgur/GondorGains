<?php

namespace Lexgur\GondorGains\Tests\Script\RunMigrationsScriptTest\FailedMigrate;

use Lexgur\GondorGains\Script\MigrationInterface;

class FailedMigration implements MigrationInterface {

    public function order(): int
    {
        return 2;
    }

    public function migrate(): void
    {
        throw new \RuntimeException(sprintf('%s has failed', static::class));
    }
}