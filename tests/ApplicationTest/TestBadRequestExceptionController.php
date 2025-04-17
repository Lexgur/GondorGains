<?php

namespace Lexgur\GondorGains\Tests\ApplicationTest;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Exception\BadRequestException;

#[Path('/test/bad-request')]
class TestBadRequestExceptionController
{
    public function __invoke(): string
    {
        throw new BadRequestException('Simulated bad request');
    }
}