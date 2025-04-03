<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Validation;

use Lexgur\GondorGains\Exception\IncorrectScriptNameException;

class ScriptNameValidator
{
    public static function validate(string $scriptName): bool
    {
        if (!preg_match('/^((\\\\?|\/?)[A-Za-z_][\w*)([\\\\\/][A-Za-z_]\w*)*$/', $scriptName) || !str_contains($scriptName, '\\')) {
            throw new IncorrectScriptNameException('Invalid namespace');
        }
        return true;
    }
}
