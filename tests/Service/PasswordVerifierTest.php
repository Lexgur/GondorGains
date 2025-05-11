<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests\Service;

use Lexgur\GondorGains\Service\PasswordVerifier;
use PHPUnit\Framework\TestCase;

class PasswordVerifierTest extends TestCase
{
    public function testVerifyReturnsTrueForCorrectPassword(): void
    {
        $password = 'testPassword123';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $passwordVerifier = new PasswordVerifier();

        $this->assertTrue($passwordVerifier->verify($password, $hashedPassword));
    }

    public function testVerifyReturnsFalseForIncorrectPassword(): void
    {
        $correctPassword = 'testPassword123';
        $wrongPassword = 'WrongPassword123';
        $hashedPassword = password_hash($correctPassword, PASSWORD_DEFAULT);
        $passwordVerifier = new PasswordVerifier();

        $this->assertFalse($passwordVerifier->verify($wrongPassword, $hashedPassword));
    }
}
