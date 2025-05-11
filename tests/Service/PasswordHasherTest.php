<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests\Service;

use Lexgur\GondorGains\Exception\WeakPasswordException;
use Lexgur\GondorGains\Service\PasswordHasher;
use PHPUnit\Framework\TestCase;

class PasswordHasherTest extends TestCase
{
    /**
     * @throws WeakPasswordException
     */
    public function testPasswordHashesSuccessfully(): void
    {
        $password = 'testPassword123';
        $hashedPassword = PasswordHasher::hash($password);

        $this->assertNotEquals($password, $hashedPassword);
    }

    public function testEmptyPasswordThrowsWeakPasswordException(): void
    {
        $this->expectException(WeakPasswordException::class);

        $password = '';
        PasswordHasher::hash($password);
    }
}
