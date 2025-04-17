<?php

namespace Lexgur\GondorGains\Tests\ApplicationTest;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Controller\AbstractController;
use Lexgur\GondorGains\Exception\BadRequestException;

#[Path('/test/bad-request')]
class TestBadRequestExceptionController extends AbstractController
{
    public function __invoke(): string
    {
        throw new BadRequestException('Simulated bad request');
    }
}