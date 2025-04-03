<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Validation;

use Lexgur\GondorGains\Exception\IncorrectScriptNameException;

class ScriptNameValidator
{
    public static function validate(string $scriptName): bool
    {
        if (preg_match('/^\?[A-Za-z_][A-Za-z0-9_]*(?:[\/][A-Za-z_][A-Za-z0-9_]*)*$/', $scriptName)) {
            return true;
        }

        throw new IncorrectScriptNameException('Invalid namespace');
    }
}
