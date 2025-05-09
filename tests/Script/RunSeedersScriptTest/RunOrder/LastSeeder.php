<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests\Script\RunSeedersScriptTest\RunOrder;

use Lexgur\GondorGains\Script\SeederInterface;

class LastSeeder implements SeederInterface
{
    public function dependencies(): array
    {
        return [FirstSeeder::class, SecondSeeder::class, ThirdSeeder::class];
    }


    public function seed(): void
    {
        echo static::class . PHP_EOL;
    }
}