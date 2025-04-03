<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Exception\IncorrectScriptNameException;
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

    public static function provideTestSuccessfulScriptExecutableData(): array
    {
        return [
            ['Lexgur\\\GondorGains\\\Tests\\\Script\\\SuccessfulScript'],
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
        $this->expectException(IncorrectScriptNameException::class);

        exec(sprintf('php ../bin/script %s', $scriptClassName), result_code: $return);
        $this->assertEquals(255, $return);
    }

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
