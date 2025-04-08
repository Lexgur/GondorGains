<?php

namespace Lexgur\GondorGains\Tests\Script\RunMigrationsScriptTest\RunOrder;

use Lexgur\GondorGains\Script\MigrationInterface;

class ThirdMigration implements MigrationInterface

{
    public function run(): int
    {
        return 0;
    }
}