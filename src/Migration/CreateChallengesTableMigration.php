<?php

namespace Lexgur\GondorGains\Migration;

use Lexgur\GondorGains\Connection;
use Lexgur\GondorGains\Script\MigrationInterface;

class CreateChallengesTableMigration implements MigrationInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function order(): int
    {
        return 3;
    }

    public function migrate(): void
    {
        echo static::class.PHP_EOL;
        $this->createTable();
    }

    private function createTable(): void
    {
        $database = $this->connection->connect();

        $database->exec('
        CREATE TABLE IF NOT EXISTS challenges (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            started_at DATETIME NOT NULL,
            completed_at DATETIME,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
    ');
    }
}
