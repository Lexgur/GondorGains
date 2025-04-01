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
        $this->database = 'sqlite:';
    }

    public function testSuccessfulConnection(): void
    {
        $connection = new Connection($this->database . './tmp/testSuccessfulConnection.sqlite');
        $validConnection = $connection->connect();

        $this->assertInstanceOf(PDO::class, $validConnection);
    }

    public function tearDown(): void
    {

        if (file_exists('./tmp/testSuccessfulConnection.sqlite')) {
            unlink('./tmp/testSuccessfulConnection.sqlite');
        }
    }
}
