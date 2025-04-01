<?php


use Lexgur\GondorGains\Connection;
use PHPUnit\Framework\TestCase;

class FailedConnectionThrowsExceptionTest extends TestCase
{
    protected function setUp(): void
    {
        $this->dbFile = './tmp/FailedConnectionThrowsExceptionTest.sqlite';
        $this->connection = new Connection('sqlite:' . $this->dbFile);
    }
    public function testFailedConnection(): void
    {
        $this->expectException(PDOException::class);

        $invalidConnection = new Connection('sqlite:/invalid/path/to/FailedConnectionThrowsExceptionTest.sqlite');
        $invalidConnection->connect();
    }
    public function tearDown(): void
    {
        $this->connection = null;

        if (file_exists($this->dbFile)) {
            unlink($this->dbFile);
        }
    }
}
