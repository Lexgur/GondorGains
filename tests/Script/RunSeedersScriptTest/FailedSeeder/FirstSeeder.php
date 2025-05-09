<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests\Script\RunSeedersScriptTest\FailedSeeder;

use Lexgur\GondorGains\Script\SeederInterface;

class FirstSeeder implements SeederInterface
{
    public function dependencies(): array
    {
        return [];
    }

    public function seed(): void
    {
    }
}