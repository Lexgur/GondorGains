<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Validation;

use Lexgur\GondorGains\Exception\EmailValidationException;
use Lexgur\GondorGains\Exception\UsernameValidationException;
use Lexgur\GondorGains\Repository\UserModelRepository;
use Lexgur\GondorGains\Model\User;

class UserModelValidator
{
    private UserModelRepository $repository;

    public function __construct(UserModelRepository $repository)
    {
        $this->repository = $repository;
    }

    public function validate(User $user): bool
    {
        $this->validateEmail($user->getUserEmail());
        $this->validateNotUsedEmail($user->getUserEmail(), $user->getUserId());
        $this->validateUsername($user->getUsername());

        return true;
    }

    public function validateEmail(string $userEmail): void
    {
        if (empty($userEmail)) {
            throw new EmailValidationException('User email cannot be empty');
        }
        if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            throw new EmailValidationException('Invalid email format');
        }
    }

    public function validateNotUsedEmail(string $userEmail, ?int $userId = null): void
{
    $existingUser = $this->repository->findByEmail($userEmail);

    if ($existingUser !== null && $existingUser->getUserId() !== $userId) {
        throw new EmailValidationException('Email is already in use');
    }
}

    public function validateUsername(string $username): void
    {
        $username = trim($username);

        if (empty($username)) {
            throw new UsernameValidationException('Username can not be empty');
        }
        if (!preg_match('/^[A-Za-z][A-Za-z0-9]{5,31}$/' ,$username)) {
            throw new UsernameValidationException('Username must start with a letter, contain only letters and numbers, and be 6–32 characters long. Underscores and special characters are not allowed.');
        }
    }
}