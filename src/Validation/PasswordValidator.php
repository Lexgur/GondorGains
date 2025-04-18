<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Validation;

use Lexgur\GondorGains\Exception\WeakPasswordException;

class PasswordValidator implements ValidatorInterface
{
    public function validate(mixed $input): bool
    {
        if (strlen($input) < 8) {
            throw new WeakPasswordException('Password must be at least 8 characters long');
        }
        if (!preg_match("/[0-9]/", $input)) {
            throw new WeakPasswordException('Password must include at least one number');
        }
        if (!preg_match("/\p{Lu}/u", $input)) {
            throw new WeakPasswordException('Password must include at least one uppercase letter');
        }
        if (!preg_match("/\p{Ll}/u", $input)) {
            throw new WeakPasswordException('Password must include at least one lowercase letter');
        } else {
            
            return true;
        }
    }
}