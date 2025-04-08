<?php

namespace Lexgur\GondorGains\Tests\Script\RunMigrationsScriptTest\FailedMigrate;

use Lexgur\GondorGains\Script\MigrationInterface;

class FailedMigration implements MigrationInterface {
    public function run(): int
    {
        return 0;
    }
}