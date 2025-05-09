<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests\Script\RunSeedersScriptTest\RunOrder;

use Lexgur\GondorGains\Script\SeederInterface;

class SecondSeeder implements SeederInterface
{
    public static function dependencies(): array
    {
        return [FirstSeeder::class];
    }

    public function seed(): void
    {

    }
}