<?php 

declare(strict_types=1);

namespace Lexgur\GondorGains\Factory;

use Lexgur\GondorGains\Model\User;

class UserModelFactory
{
    public function __construct(){

    }

    public static function create(array $data): User
    {
        return new User(
            userEmail: $data['email'] ?? '',
            username: $data['username'] ?? '',
            userPassword: $data['password'] ?? '',
            userId: $data['id'] ?? null
        );
    }
}