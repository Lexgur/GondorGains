<?php

declare(strict_types=1);

use Lexgur\GondorGains\Script;
use PHPUnit\Framework\TestCase;

class ScriptTest extends TestCase
{
    private Script $script;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tmpTestDir = __DIR__ . '/../tmp/tests/';
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        array_map('unlink', glob($this->tmpTestDir . '*.php'));
    }

}
