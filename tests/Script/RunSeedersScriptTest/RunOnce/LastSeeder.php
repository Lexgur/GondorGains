<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests\Script\RunSeedersScriptTest\RunOnce;

use Lexgur\GondorGains\Script\SeederInterface;

class LastSeeder implements SeederInterface
{
    public function order(): int
    {
        return 2;
    }

    public function seed(): void
    {
        echo static::class . PHP_EOL;
    }
}