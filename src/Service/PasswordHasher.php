<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Service;

use Lexgur\GondorGains\Exception\WeakPasswordException;

class PasswordHasher
{
    public static function hash(string $password): string
    {
        if(!empty($password)) {
            return password_hash($password, PASSWORD_DEFAULT);
        } else {
            throw new WeakPasswordException('Password is empty');
        }
    }
}