<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
class ScriptExecutableTest extends TestCase
{
    #[DataProvider('provideTestSuccessfulScriptExecutableData')]
    public function testSuccessfulScriptExecutable(string $scriptClassName): void
    {
        exec(sprintf("php ../bin/script %s", $scriptClassName), $output, $return);

        $this->assertEquals(0, $return);
        $this->assertCount(1, $output);
        $this->assertEquals('Hello World!', $output[0]);
    }

    /** @return array<array{string}> */
    public static function provideTestSuccessfulScriptExecutableData(): array
    {
        return [
            ["Lexgur\\\GondorGains\\\Tests\\\Script\\\SuccessfulScript"],
            ['\\\Lexgur\\\GondorGains\\\Tests\\\Script\\\SuccessfulScript'],
            ['Lexgur/GondorGains/Tests/Script/SuccessfulScript'],
            ['/Lexgur/GondorGains/Tests/Script/SuccessfulScript'],
            ['"Lexgur\GondorGains\Tests\Script\SuccessfulScript"'],
            ['"\Lexgur\GondorGains\Tests\Script\SuccessfulScript"'],
        ];
    }

    #[DataProvider('provideTestFailedScriptExecutableData')]
    public function testFailedScriptExecutable(string $scriptClassName): void
    {
        exec(sprintf('php ../bin/script %s', $scriptClassName), $output, $return);

        $this->assertEquals(1, $return);
        $this->assertCount(1, $output);
        $this->assertEquals('Not so hello World!', $output[0]);
    }

    /** @return array<array{string}> */
    public static function provideTestFailedScriptExecutableData(): array
    {
        return [
            ['Lexgur\\\GondorGains\\\Tests\\\Script\\\FailedScript'],
            ['\\\Lexgur\\\GondorGains\\\Tests\\\Script\\\FailedScript'],
            ['Lexgur/GondorGains/Tests/Script/FailedScript'],
            ['/Lexgur/GondorGains/Tests/Script/FailedScript'],
            ['"Lexgur\GondorGains\Tests\Script\FailedScript"'],
            ['"\Lexgur\GondorGains\Tests\Script\FailedScript"'],
        ];
    }

    #[DataProvider('provideTestIncorrectlyWrittenScriptCallExecutableData')]
    public function testIncorrectlyWrittenScriptCallExecutable(string $scriptClassName): void
    {
        $this->markTestSkipped('This test works on linux, but not on windows and should be skipped.');
        /** @phpstan-ignore deadCode.unreachable */
        exec(sprintf('php ../bin/script %s', $scriptClassName), result_code: $return);
        $this->assertEquals(255, $return);
    }

    /** @return array<array{string}> */
    public static function provideTestIncorrectlyWrittenScriptCallExecutableData(): array
    {
        return [
            ['Lexgur\\GondorGains\\Tests\\Script\\FailedScript'],
            ['\\Lexgur\\GondorGains\\Tests\\Script\\FailedScript'],
            ['Lexgur/GondorGains/Tests/Script//FailedScript'],
            ['//Lexgur/GondorGains/Tests/Script/FailedScript'],
            ['Lexgur\GondorGains\Tests\Script\FailedScript'],
            ['\Lexgur\GondorGains\Tests\Script\FailedScript'],
        ];
    }
}
