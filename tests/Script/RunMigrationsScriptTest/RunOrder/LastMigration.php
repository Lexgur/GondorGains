<?php

namespace Lexgur\GondorGains\Tests\Script\RunMigrationsScriptTest\RunOrder;

use Lexgur\GondorGains\Script\MigrationInterface;

class LastMigration implements MigrationInterface

{
    public function order(): int
    {
        return 4;
    }

    public function migrate(): void
    {
        echo static::class . PHP_EOL;
    }
}