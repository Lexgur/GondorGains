<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Connection;
use PHPUnit\Framework\TestCase;

class FailedConnectionThrowsExceptionTest extends TestCase
{
    public function testFailedConnection(): void
    {
        $connection = new Connection('sqlite:/invalid/path/to/FailedConnectionThrowsExceptionTest.sqlite');

        $this->expectException(\PDOException::class);

        $connection->connect();
    }
}
