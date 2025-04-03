<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NameSpaceRegexTest extends TestCase {

    #[DataProvider('provideTestNamespaceRegexData')]
    public function testNamespaceRegex(string $scriptClassName, int $expected): void
    {
        $actual = preg_match('/^((\\\\?|\/?)[A-Za-z_][\w*)([\\\\\/][A-Za-z_]\w*)*$/', $scriptClassName);

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
