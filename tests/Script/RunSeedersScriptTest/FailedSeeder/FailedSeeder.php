<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests\Script\RunSeedersScriptTest\FailedSeeder;

use Lexgur\GondorGains\Script\SeederInterface;

class FailedSeeder implements SeederInterface
{
    public function dependencies(): array
    {
        return [FirstSeeder::class];
    }

    public function seed(): void
    {
        throw new \RuntimeException(sprintf('%s has failed', static::class));
    }
}