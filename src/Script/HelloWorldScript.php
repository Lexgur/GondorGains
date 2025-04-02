<?php

namespace Lexgur\GondorGains\Script;

class HelloWorldScript implements ScriptInterface {
    public function run(): string
    {
        return 'Hello world';
    }
}