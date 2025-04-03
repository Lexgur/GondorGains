<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Validation\ScriptNameValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ScriptNameValidatorTest extends TestCase {

    protected function setUp(): void
    {
        $this->validator = new ScriptNameValidator();
    }

    #[DataProvider('provideTestNamespaceRegexData')]
    public function testNamespaceRegex(string $scriptClassName, int $expected): void
    {
        $actual = $this->validator->validate($scriptClassName);

        $this->assertEquals($expected, $actual);
    }

    public static function provideTestNamespaceRegexData(): array
    {
        return [
            ["Lexgur\GondorGains\Tests\Script\FailedScript", 1],
            ["\Lexgur\GondorGains\Tests\Script\FailedScript", 1],
            ['Lexgur\\GondorGains\\Tests\\Script\\FailedScript', 1],
            ['\\Lexgur\\GondorGains\\Tests\\Script\\FailedScript', 1],
            ['Lexgur/GondorGains/Tests/Script/FailedScript', 1],
            ['/Lexgur/GondorGains/Tests/Script/FailedScript', 1],
            ["Lexgur\\\GondorGains\Tests\Script\FailedScript", 0],
            ["\\\Lexgur\GondorGains\Tests\Script\FailedScript", 0],
        ];
    }

}
