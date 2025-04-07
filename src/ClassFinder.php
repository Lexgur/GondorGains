<?php

declare(strict_types=1);

namespace Lexgur\GondorGains;

use _PHPStan_f2f2ddf44\Nette\Neon\Exception;

class ClassFinder
{
    public function findClassesImplementing(string $value) : array
    {
        throw new Exception('Ooopsie');
    }

    public function findClassesExtending(string $value) : array
    {
        throw new Exception('Uh-oh');
    }

    public function findClassesInNamespace(string $value) : array
    {
        throw new Exception('Not good');
    }
}