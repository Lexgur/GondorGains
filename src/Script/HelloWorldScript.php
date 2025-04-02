<?php

namespace Lexgur\GondorGains\Script;

class HelloWorldScript implements ScriptInterface {
    public function run(): int
    {
        echo 'Hello world';
        exit(1);
    }
}