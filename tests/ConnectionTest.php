<?php

use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ConnectionTest extends TestCase
{
    private string $testDbPath;
    private ?PDO $pdo = null;

    protected function setUp(): void
    {
        $this->testDbPath = __DIR__.'/test_database.sqlite';

        if (file_exists($this->testDbPath)) {
            unlink($this->testDbPath);
        }
    }
    public function testSuccessfulConnection(): void
    {
        $this->pdo = new PDO('sqlite:'.$this->testDbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->assertNotNull($this->pdo);
    }

    public function testFailedConnection(): void
    {
        $this->expectException(PDOException::class);

        new PDO('sqlite:/invalid/path/to/non_existent.sqlite');
    }

    protected function tearDown(): void
    {
        $this->pdo = null;

        if (file_exists($this->testDbPath)) {
            unlink($this->testDbPath);
        }
    }

}
