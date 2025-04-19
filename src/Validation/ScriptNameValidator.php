<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Validation;

use Lexgur\GondorGains\Exception\IncorrectScriptNameException;

class ScriptNameValidator implements ValidatorInterface
{
    public function validate(mixed $input): bool
    {
        if (!strpbrk($input, '/\\')) {
            throw new IncorrectScriptNameException("Invalid script name: {$input}");
        }

        if (!preg_match('/^((\\\?|\/?)[A-Za-z_]\w*)([\\\\\/][A-Za-z_]\w*)*$/', $input)) {
            throw new IncorrectScriptNameException("Invalid namespace format: {$input}");
        }

        return true;
    }
}
