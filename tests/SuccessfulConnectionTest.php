<?php

declare(strict_types=1);

use Lexgur\GondorGains\Connection;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class SuccessfulConnectionTest extends TestCase
{
    private string $testDatabaseFile;
    protected function setUp(): void
    {
        $testName = $this->name();
        $this->testDatabaseFile = sprintf('../tmp/tests/%s%s.sqlite', $testName, uniqid('test_', true));
    }

    public function testSuccessfulConnection(): void
    {
        $connection = new Connection(sprintf('sqlite:' . $this->testDatabaseFile));
        $validConnection = $connection->connect();

        $this->assertInstanceOf(PDO::class, $validConnection);
    }

    public function tearDown(): void
    {
            unlink($this->testDatabaseFile);
    }
}
