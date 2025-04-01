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
        $this->connection = new Connection('sqlite:./tmp/SuccessfulConnectionTest.sqlite');
    }

    public function testSuccessfulConnection(): void
    {

        $validConnection = $this->connection->connect();

        $this->assertInstanceOf(PDO::class, $validConnection);
    }

    public function tearDown(): void
    {
        $this->connection = null;

        if (file_exists('./tmp/SuccessfulConnectionTest.sqlite')) {
            unlink('./tmp/SuccessfulConnectionTest.sqlite');
        }
    }
}
