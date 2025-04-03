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
        $scriptClass =  'Lexgur\\GondorGains\\Tests\\Script\\SuccessfulScript';
        $this->validator->validate($scriptClass);

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertTrue(true);
    }

    public function testValidationWithIncorrectScriptStartThrowsIncorrectScriptNameException(): void {
        $this->expectException(IncorrectScriptNameException::class);
        $this->expectExceptionMessage('Invalid namespace');

        $scriptClass = '//Lexgur/GondorGains/Script/';
        $this->validator->validate($scriptClass);
    }

    public function testValidationWithoutSpecifiedScriptThrowsIncorrectScriptNameException(): void {
        $this->expectException(IncorrectScriptNameException::class);
        $this->expectExceptionMessage('Invalid namespace');

        $scriptClass = 'Lexgur/GondorGains/Script/';
        $this->validator->validate($scriptClass);
    }
}
