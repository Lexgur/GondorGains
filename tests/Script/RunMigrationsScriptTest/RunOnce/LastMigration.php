<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests\Script\RunMigrationsScriptTest\RunOnce;

use Lexgur\GondorGains\Script\MigrationInterface;

class LastMigration implements MigrationInterface
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