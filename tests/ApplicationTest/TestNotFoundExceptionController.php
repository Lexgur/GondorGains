<?php

namespace Lexgur\GondorGains\Tests\ApplicationTest;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Exception\NotFoundException;

#[Path('/test/notfound')]
class TestNotFoundExceptionController
{
    public function __invoke(): string
    {
        throw new NotFoundException('Simulated notfound');
    }
}