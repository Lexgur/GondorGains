<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Script;

class HelloWorldScript implements ScriptInterface {
    public function run(): int {
        echo 'Hello world';
        return 0;
    }
}