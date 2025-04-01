<?php

declare(strict_types=1);

use Lexgur\GondorGains\Connection;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class SuccessfulConnectionTest extends TestCase
{
    protected function setUp(): void
    {
        $this->dbFile = './tmp/SuccessfulConnectionTest.sqlite';
        $this->connection = new Connection('sqlite:' . $this->dbFile);
    }

    public function testSuccessfulConnection(): void
    {

        $validConnection = $this->connection->connect();

        $this->assertInstanceOf(PDO::class, $validConnection);
    }

    public function tearDown(): void
    {
        $this->connection = null;

        if (file_exists($this->dbFile)) {
            unlink($this->dbFile);
        }
    }
}
