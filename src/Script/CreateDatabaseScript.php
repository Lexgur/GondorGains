<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Script;

use Lexgur\GondorGains\Connection;

class CreateDatabaseScript implements ScriptInterface
{
    private string $dsn;

    private Connection $connection;

    public function __construct(Connection $connection, string $dsn)
    {
        $this->dsn = $dsn;
        $this->connection = $connection;
    }

    public function run(): int
    {
        $parsedDsn = parse_url($this->dsn);
        $dbPath = $parsedDsn['path'];

        $dir = dirname($dbPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        if (file_exists($dbPath)) {
            throw new \Exception('Database file already exists');
        }

        $this->connection->connect();

        return 0;
    }
}
