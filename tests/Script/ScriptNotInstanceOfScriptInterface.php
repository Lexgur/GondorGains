<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests\Script;

class ScriptNotInstanceOfScriptInterface
{
    public function run(): int
    {
        echo 'Oopses!';

        return 0;
    }

}