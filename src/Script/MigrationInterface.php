<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Script;

interface MigrationInterface
{
    public function order(): int;

    public function migrate(): void;
}