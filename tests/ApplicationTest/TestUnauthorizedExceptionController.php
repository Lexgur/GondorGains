<?php

namespace Lexgur\GondorGains\Tests\ApplicationTest;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Controller\AbstractController;
use Lexgur\GondorGains\Exception\UnauthorizedException;

#[Path('/test/unauthorized')]
class TestUnauthorizedExceptionController extends AbstractController
{
    public function __invoke(): string
    {
        throw new UnauthorizedException('Simulated unauthorized');
    }
}