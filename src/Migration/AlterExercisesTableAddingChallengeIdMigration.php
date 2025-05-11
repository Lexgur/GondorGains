<?php

namespace Lexgur\GondorGains\Migration;

use Lexgur\GondorGains\Connection;
use Lexgur\GondorGains\Script\MigrationInterface;

class AlterExercisesTableAddingChallengeIdMigration implements MigrationInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function order(): int
    {
        return 4;
    }

    public function migrate(): void
    {
        echo static::class . PHP_EOL;
        $this->addChallengeIdColumn();
    }

    private function addChallengeIdColumn(): void
    {
        $database = $this->connection->connect();

        $database->exec("
            ALTER TABLE exercises 
            ADD COLUMN challenge_id INTEGER REFERENCES challenges(id) ON DELETE SET NULL;
        ");
    }
}