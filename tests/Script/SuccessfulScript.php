<?php

namespace Lexgur\GondorGains\Tests\Script;

use Lexgur\GondorGains\Script\ScriptInterface;

class SuccessfulScript implements ScriptInterface
{
    public function run(): int
    {
        echo 'Hello World!';

        return 0;
    }

}