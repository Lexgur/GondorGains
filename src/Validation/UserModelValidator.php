<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Validation;

use Lexgur\GondorGains\Validation\ValidatorInterface;
use Lexgur\GondorGains\Exception\EmailValidationException;
use Lexgur\GondorGains\Exception\UsernameValidationException;
use Lexgur\GondorGains\Model\User;

class UserModelValidator implements ValidatorInterface
{
    public function validate(mixed $input): bool
    {  
        $this->validateEmail($input->getUserEmail());
        $this->validateUsername($input->getUsername());

        return true;
    }

    public function validateEmail(string $userEmail): bool
    {
        if (empty($userEmail)) {
            throw new EmailValidationException('User email cannot be empty');
        }
        if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            throw new EmailValidationException('Invalid email format');
        }
        return true;
    }

    public function validateUsername(string $username): bool
    {
        $username = trim($username);

        if (empty($username)) {
            throw new UsernameValidationException('Username can not be empty');
        }
        if (!preg_match('/^[A-Za-z][A-Za-z0-9]{5,31}$/' ,$username)) {
            throw new UsernameValidationException('Username must start with a letter, contain only letters and numbers, and be 6–32 characters long. Underscores and special characters are not allowed.');
        }

        return true;
    }
}