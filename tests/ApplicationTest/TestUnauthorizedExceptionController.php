<?php

namespace Lexgur\GondorGains\Tests\ApplicationTest;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Exception\UnauthorizedException;

#[Path('/test/unauthorized')]
class TestUnauthorizedExceptionController
{
    public function __invoke(): string
    {
        throw new UnauthorizedException('Simulated unauthorized');
    }
}