<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Script;

interface SeederInterface
{
    public function order(): int;

    public function seed(): void;
}