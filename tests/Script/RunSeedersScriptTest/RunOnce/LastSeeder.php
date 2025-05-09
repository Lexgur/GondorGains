<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests\Script\RunSeedersScriptTest\RunOnce;

use Lexgur\GondorGains\Script\SeederInterface;

class LastSeeder implements SeederInterface
{
    public function dependencies(): array
    {
        return [FirstSeeder::class];
    }

    public function seed(): void
    {
    }
}