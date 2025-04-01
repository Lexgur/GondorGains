<?php

use Lexgur\GondorGains\Connection;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class FailedConnectionThrowsExceptionTest extends TestCase
{
    public function testFailedConnection(): void
    {
        $connection = new Connection('sqlite:/invalid/path/to/FailedConnectionThrowsExceptionTest.sqlite');

        $this->expectException(PDOException::class);

        $connection->connect();
    }
}
