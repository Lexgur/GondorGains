<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class Path
{
    public function __construct(
        private string $path
    ) {
    }
    public function getPath(): string
    {
        return $this->path;
    }
}