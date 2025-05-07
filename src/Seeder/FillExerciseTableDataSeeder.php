<?php

namespace Lexgur\GondorGains\Seeder;

use Lexgur\GondorGains\Connection;
use Lexgur\GondorGains\Script\SeederInterface;

class FillExerciseTableDataSeeder implements SeederInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function order(): int
    {
        return 1;
    }

    public function seed(): void
    {
        echo static::class.PHP_EOL;
        $this->fillTable();
    }

    public function fillTable(): void
    {

    }
}