<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests\Script\RunSeedersScriptTest\RunOrder;

use Lexgur\GondorGains\Script\SeederInterface;

class FirstSeeder implements SeederInterface
{
    public function order(): int
    {
        return 1;
    }

    public function seed(): void
    {
        echo static::class . PHP_EOL;
    }
}