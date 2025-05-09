<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Script;

interface SeederInterface
{
    /**
     * Returns an array of class names this seeder depends on.
     *
     * @return string[]
     */
    public function dependencies(): array;

    public function seed(): void;
}