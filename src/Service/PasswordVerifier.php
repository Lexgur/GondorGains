<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Service;

class PasswordVerifier
{
    public static function verify(string $password, string $hashedPassword): bool
    {
        return password_verify($password, $hashedPassword);
    }
}