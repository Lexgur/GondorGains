<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests\Script\RunSeedersScriptTest\RunOrder;

use Lexgur\GondorGains\Script\SeederInterface;

class ThirdSeeder implements SeederInterface
{
    public static function dependencies(): array
    {
        return [FirstSeeder::class, SecondSeeder::class];
    }

    public function seed(): void
    {
    }
}