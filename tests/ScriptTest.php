<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Exception\IncorrectScriptNameException;
use Lexgur\GondorGains\Script;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ScriptTest extends TestCase
{
    private Script $script;

    protected function setUp(): void
    {
        $this->script = new Script();
    }

    #[DataProvider('provideTestSuccessfulScriptData')]
    public function testSuccessfulScript(string $scriptClassName): void
    {
        $this->expectOutputString('Hello World!');

        $result = $this->script->run($scriptClassName);

        $this->assertEquals(0, $result);
    }

    /** @return array<array{string}> */
    public static function provideTestSuccessfulScriptData(): array
    {
        return [
            ['Lexgur\GondorGains\Tests\Script\SuccessfulScript'],
            ['\Lexgur\GondorGains\Tests\Script\SuccessfulScript'],
            ['Lexgur/GondorGains/Tests/Script/SuccessfulScript'],
            ['/Lexgur/GondorGains/Tests/Script/SuccessfulScript'],
        ];
    }

    #[DataProvider('provideTestFailedScriptData')]
    public function testFailedScript(string $scriptClassName): void
    {
        $this->expectOutputString('Not so hello World!');

        $result = $this->script->run($scriptClassName);

        $this->assertEquals(1, $result);
    }

    /** @return array<array{string}> */
    public static function provideTestFailedScriptData(): array
    {
        return [
            ['Lexgur\GondorGains\Tests\Script\FailedScript'],
            ['\Lexgur\GondorGains\Tests\Script\FailedScript'],
            ['Lexgur/GondorGains/Tests/Script/FailedScript'],
            ['/Lexgur/GondorGains/Tests/Script/FailedScript'],
        ];
    }

    #[DataProvider('provideTestScriptFailedToRunExceptionData')]
    public function testScriptFailedToRunException(string $scriptClassName): void
    {
        $this->expectException(IncorrectScriptNameException::class);

        $this->script->run($scriptClassName);
    }

    /** @return array<array{string}> */
    public static function provideTestScriptFailedToRunExceptionData(): array
    {
        return [
            ['LexgurGondorGainsTestsScriptFailedScript'],
            ['Lexgur/GondorGains/Tests/Script//FailedScript'],
            ['//Lexgur/GondorGains/Tests/Script/FailedScript'],
        ];
    }

}