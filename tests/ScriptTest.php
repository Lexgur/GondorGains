<?php

declare(strict_types=1);

use Lexgur\GondorGains\Exception\ScriptFailedToRunException;
use Lexgur\GondorGains\Script;
use PHPUnit\Framework\TestCase;

class ScriptTest extends TestCase
{
    private string $tmpTestDir;

    protected function setUp(): void {
        parent::setUp();

        $this->tmpTestDir = __DIR__ . '/../tmp/tests/';
    }

    public function testRunValidScript(): void {
        $scriptClass = 'Lexgur/GondorGains/Script/TempTestScript';
        $filePath = $this->tmpTestDir . 'TempTestScript.php';

        $namespace = "Lexgur\\GondorGains\\Script";
        $className = "TempTestScript";

        $scriptContent = <<<PHP
        <?php
        namespace $namespace;

        use Lexgur\GondorGains\Script\ScriptInterface;

        class $className implements ScriptInterface {
            public function run(): int {
                return 1;
            }
        }
        PHP;

        file_put_contents($filePath, $scriptContent);

        require_once $filePath;
        $script = new Script();
        $result = $script->run($scriptClass);

        $this->assertSame(1, $result);
    }

    public function testRunInvalidScriptThrowsScriptFailedToRunException(): void {
        $this->expectException(ScriptFailedToRunException::class);

        $script = new Script();
        $script->run('Lexgur/GondorGains/Script/NonExistentScript');
    }

    protected function tearDown(): void {
        parent::tearDown();
        array_map('unlink', glob($this->tmpTestDir . '*.php'));
    }

}
