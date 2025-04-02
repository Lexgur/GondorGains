<?php

declare(strict_types=1);

use Lexgur\GondorGains\Exception\IncorrectScriptNameException;
use Lexgur\GondorGains\Validation\ScriptNameValidation;
use PHPUnit\Framework\TestCase;

class ScriptNameValidationTest extends TestCase {
    private ScriptNameValidation $validation;

    protected function setUp(): void
    {
        $this->validation = new ScriptNameValidation();
    }

    public function testSuccessfulValidation(): void {
        $scriptClass = 'Lexgur/GondorGains/Script/HelloWorldScript';
        $this->validation->validate($scriptClass);

        $this->assertTrue(true);
    }

    public function testValidationWithIncorrectScriptStartThrowsIncorrectScriptNameException(): void {
        $this->expectException(IncorrectScriptNameException::class);
        $this->expectExceptionMessage('Script should start with Lexgur/GondorGains/Script/');

        $scriptClass = '//Lexgur/GondorGains/Script/';
        $this->validation->validate($scriptClass);
    }

    public function testValidationWithoutSpecifiedScriptThrowsIncorrectScriptNameException(): void {
        $this->expectException(IncorrectScriptNameException::class);
        $this->expectExceptionMessage('Script name must include a valid class after Lexgur/GondorGains/Script/');

        $scriptClass = 'Lexgur/GondorGains/Script/';
        $this->validation->validate($scriptClass);
    }
}
