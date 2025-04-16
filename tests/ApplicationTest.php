<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Application;
use Lexgur\GondorGains\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    private Application $app;

    protected function setUp(): void
    {
        $this->app = new Application();
    }

    public function testNotFoundExceptionWithAnEmptyRequest(): void
    {

    }

}