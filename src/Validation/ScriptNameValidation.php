<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Validation;

use Lexgur\GondorGains\Exception\IncorrectScriptNameException;

class ScriptNameValidation
{
    public static function validate(string $scriptName): bool
    {
        $scriptName = trim($scriptName);
        $scriptName = preg_replace('/\/+/', '/', $scriptName);

        if (!str_starts_with($scriptName, "Lexgur/GondorGains/Script/")) {
            throw new IncorrectScriptNameException('Script should start with Lexgur/GondorGains/Script/');
        }

        if ($scriptName === "Lexgur/GondorGains/Script/") {
            throw new IncorrectScriptNameException('Script name must include a valid class after Lexgur/GondorGains/Script/');
        }

        return true;
    }
}