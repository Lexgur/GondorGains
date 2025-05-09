<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests\Script\RunSeedersScriptTest\RunOrder;

use Lexgur\GondorGains\Script\SeederInterface;

class FirstSeeder implements SeederInterface
{
    public static function dependencies(): array
    {
        return [];
    }

    public function seed(): void
    {
    }
}