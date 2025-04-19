<?php 

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Exception\WeakPasswordException;
use Lexgur\GondorGains\Validation\PasswordValidator;
use PHPUnit\Framework\TestCase;

class PasswordValidatorTest extends TestCase
{
    private PasswordValidator $validator;

    public function setUp(): void
    {
        $this->validator = new PasswordValidator();
    }

    public function testShortPasswordThrowsWeakPasswordException(): void
    {
        $this->expectException(WeakPasswordException::class);

        $password = 'daviD77';
        $this->validator->validate($password);
    }

    public function testPasswordWithoutNumbersThrowsWeakPasswordException(): void
    {
        $this->expectException(WeakPasswordException::class);

        $password = 'justapassword';
        $this->validator->validate($password);
    }

    public function testPasswordWithoutUppercaseLettersThrowsWeakPasswordException(): void
    {
        $this->expectException(WeakPasswordException::class);

        $password = 'password17774';
        $this->validator->validate($password);
    }

    public function testPasswordWithoutLowercaseLettersThrowsWeakPasswordException(): void
    {
        $this->expectException(WeakPasswordException::class);

        $password = 'PASSWORD17774';
        $this->validator->validate($password);
    }
}