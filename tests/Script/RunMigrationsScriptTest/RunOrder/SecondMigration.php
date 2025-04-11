<?php

namespace Lexgur\GondorGains\Tests\Script\RunMigrationsScriptTest\RunOrder;

use Lexgur\GondorGains\Script\MigrationInterface;

class SecondMigration implements MigrationInterface

{
    public function order(): int
    {
        return 2;
    }

    public function migrate(): void
    {
        echo static::class . PHP_EOL;
    }
}