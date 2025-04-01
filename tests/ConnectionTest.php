<?php

declare(strict_types=1);

use Lexgur\GondorGains\Connection;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ConnectionTest extends TestCase
{
    public function testSuccessfulConnection(): void
    {
        $connection = new Connection('sqlite:/tmp/test_database.sqlite');

        $databaseConnection = $connection->connect();

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
        if (file_exists('/tmp/test_database.sqlite')) {
            unlink('/tmp/test_database.sqlite');
        }
    }

}
