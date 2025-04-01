<?php


use Lexgur\GondorGains\Connection;
use PHPUnit\Framework\TestCase;

class FailedConnectionThrowsExceptionTest extends TestCase
{
    protected function setUp(): void
    {
        $this->connection = new Connection('sqlite:/invalid/path/to/FailedConnectionThrowsExceptionTest.sqlite');
    }
    public function testFailedConnection(): void
    {
        $this->expectException(PDOException::class);

        $this->connection->connect();
    }
}
