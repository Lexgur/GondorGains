<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Model;

class User
{
    private ?int $userId = null;

    private $userEmail;

    private $userPassword;

    private $username;

    public function __construct(string $userEmail, string $username, string $userPassword, ?int $userId = null)
    {
        $this->userEmail = $userEmail;
        $this->userPassword = $userPassword;
        $this->username = $username;
        $this->userId = $userId;
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function setUserEmail(string $userEmail): void
    {
        $this->userEmail = $userEmail;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getUserPassword(): string
    {
        return $this->userPassword;
    }

    public function setUserPassword(string $userPassword): void
    {
        $this->userPassword = $userPassword;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public static function create(array $data): User
    {
        return new User(
            userEmail: $data['email'],
            username: $data['username'],
            userPassword: $data['password'],
            userId: $data['id'] ?? null
        );
    }
}