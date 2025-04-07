<?php

declare(strict_types=1);

namespace Lexgur\GondorGains;

use Exception;

class ClassFinder
{
    private const DIR = __DIR__;

    private string $path;

    public function __construct($path = self::DIR)
    {
        $this->path = $path;
    }

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