<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests\Script;

use Lexgur\GondorGains\Script\ScriptInterface;

class FailedScript implements ScriptInterface {
    public function run(): int
    {
        return 1;
    }
}