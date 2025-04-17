<?php

namespace Lexgur\GondorGains\Tests\ApplicationTest;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Controller\AbstractController;
use Lexgur\GondorGains\Exception\ForbiddenException;

#[Path('/test/forbidden')]
class TestForbiddenExceptionController extends AbstractController
{
    public function __invoke(): string
    {
        throw new ForbiddenException('Simulated forbidden');
    }
}