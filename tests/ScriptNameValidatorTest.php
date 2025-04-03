<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Exception\IncorrectScriptNameException;
use Lexgur\GondorGains\Validation\ScriptNameValidator;
use PHPUnit\Framework\TestCase;

class ScriptNameValidatorTest extends TestCase {
    private ScriptNameValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new ScriptNameValidator();
    }

    public function testSuccessfulValidation(): void {
        $scriptName = 'Lexgur\\GondorGains\\Tests\\Script\\SuccessfulScript';
        $this->validator->validate($scriptName);

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertTrue(true);

        $scriptName2 = '\\Lexgur\\GondorGains\\Tests\\Script\\SuccessfulScript';
        $this->validator->validate($scriptName2);

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertTrue(true);
    }

    public function testValidationWithIncorrectScriptStartThrowsIncorrectScriptNameException(): void {
        $this->expectException(IncorrectScriptNameException::class);
        $this->expectExceptionMessage('Invalid namespace');

        $scriptName = '//Lexgur/GondorGains/Script/';
        $this->validator->validate($scriptName);
    }

    public function testValidationWithoutSpecifiedScriptThrowsIncorrectScriptNameException(): void {
        $this->expectException(IncorrectScriptNameException::class);
        $this->expectExceptionMessage('Invalid namespace');

        $scriptName = 'Lexgur/GondorGains/Script/';
        $this->validator->validate($scriptName);
    }
}
