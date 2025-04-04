<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Connection;
use PDO;
use PHPUnit\Framework\TestCase;

class SuccessfulConnectionTest extends TestCase
{
    private string $testDatabaseFile;

    protected function setUp(): void
    {
        $testName = $this->name();
        $this->testDatabaseFile = sprintf('../tmp/tests/%s%s.sqlite', $testName, uniqid('test_', true));
    }

    public function tearDown(): void
    {
        unlink($this->testDatabaseFile);
    }

    public function testSuccessfulConnection(): void
    {
        $connection = new Connection(sprintf('sqlite:%s', $this->testDatabaseFile));
        $validConnection = $connection->connect();

        $this->assertInstanceOf(PDO::class, $validConnection);
    }
}
