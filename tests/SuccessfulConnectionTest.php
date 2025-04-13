<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Connection;
use PHPUnit\Framework\TestCase;

class SuccessfulConnectionTest extends TestCase
{
    /** @var array<string>  */
    private array $testConfig;
    private string $testDatabaseFile;

    protected function setUp(): void
    {
        $this->testConfig = require __DIR__ . '/../config.php';
        $testName = $this->name();
        $testDir = ($this->testConfig['filesystem']);
        $this->testDatabaseFile = sprintf('%s/%s_%s.sqlite', $testDir, $testName, uniqid('test_', true));
    }

    public function tearDown(): void
    {
        unlink($this->testDatabaseFile);
    }

    public function testSuccessfulConnection(): void
    {
        $connection = new Connection(sprintf('sqlite:%s', $this->testDatabaseFile));
        $validConnection = $connection->connect();

        $this->assertInstanceOf(\PDO::class, $validConnection);
    }
}
