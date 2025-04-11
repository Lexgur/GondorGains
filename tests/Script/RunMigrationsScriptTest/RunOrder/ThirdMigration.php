<?php

namespace Lexgur\GondorGains\Tests\Script\RunMigrationsScriptTest\RunOrder;

use Lexgur\GondorGains\Script\MigrationInterface;

class ThirdMigration implements MigrationInterface

{
    public function order(): int
    {
        return 3;
    }

    public function migrate(): void
    {
        echo static::class . PHP_EOL;
    }
}