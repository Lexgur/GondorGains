<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Validation;

use Lexgur\GondorGains\Exception\IncorrectScriptNameException;

class ScriptNameValidator
{
    public static function validate(string $scriptName): bool
    {
        if (!strpbrk($scriptName, '/\\')) {
            throw new IncorrectScriptNameException("Invalid script name: {$scriptName}");
        }

        if (!preg_match('/^((\\\\?|\/?)[A-Za-z_]\w*)([\\\\\/][A-Za-z_]\w*)*$/', $scriptName)) {
            throw new IncorrectScriptNameException("Invalid namespace format: {$scriptName}");
        }

        return true;
    }
}
