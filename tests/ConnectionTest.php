<?php

use Lexgur\GondorGains\Connection;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ConnectionTest extends TestCase
{
    private string $testDbPath;
    private Connection $connection;


    protected function setUp(): void
    {
        $this->testDbPath = __DIR__ . '/tmp/test_database.sqlite';

        $this->connection = new Connection('sqlite:'.$this->testDbPath);
    }

    public function testSuccessfulConnection(): void
    {
        $databaseConnection = $this->connection->connect();

        $this->assertInstanceOf(\PDO::class, $databaseConnection);
    }

    public function testFailedConnection(): void
    {
        $this->expectException(PDOException::class);

        $invalidConnection = new Connection('sqlite:/invalid/path/to/non_existent.sqlite');
        $invalidConnection->connect();
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testDbPath)) {
            unlink($this->testDbPath);
        }
    }

}
