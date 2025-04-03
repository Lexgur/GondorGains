<?php

declare(strict_types=1);

use Lexgur\GondorGains\Exception\IncorrectScriptNameException;
use Lexgur\GondorGains\Validation\ScriptNameValidation;
use Lexgur\GondorGains\Validation\ScriptNameValidator;
use PHPUnit\Framework\TestCase;

class ScriptNameValidationTest extends TestCase {
    private ScriptNameValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new ScriptNameValidator();
    }

    public function testSuccessfulValidation(): void {
        $scriptClass = 'Lexgur/GondorGains/Script/HelloWorldScript';
        $this->validator->validate($scriptClass);

        /** @phpstan-ignore method.alreadyNarrowedType */
        $this->assertTrue(true);
    }

    public function testValidationWithIncorrectScriptStartThrowsIncorrectScriptNameException(): void {
        $this->expectException(IncorrectScriptNameException::class);
        $this->expectExceptionMessage('Script should start with Lexgur/GondorGains/Script/');

        $scriptClass = '//Lexgur/GondorGains/Script/';
        $this->validator->validate($scriptClass);
    }

    public function testValidationWithoutSpecifiedScriptThrowsIncorrectScriptNameException(): void {
        $this->expectException(IncorrectScriptNameException::class);
        $this->expectExceptionMessage('Script name must include a valid class after Lexgur/GondorGains/Script/');

        $scriptClass = 'Lexgur/GondorGains/Script/';
        $this->validator->validate($scriptClass);
    }
}
