<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Migration;

use Lexgur\GondorGains\Connection;
use Lexgur\GondorGains\Script\MigrationInterface;

class CreateExercisesTableMigration implements MigrationInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function order(): int
    {
        return 2;
    }

    public function migrate(): void
    {
        echo static::class . PHP_EOL;
        $this->createTable();
    }

    private function createTable(): void
    {
        $database = $this->connection->connect();

        $database->exec("
            CREATE TABLE IF NOT EXISTS exercises (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL UNIQUE,
                muscle_group TEXT NOT NULL,
                description TEXT NOT NULL
            );
        ");
    }
}