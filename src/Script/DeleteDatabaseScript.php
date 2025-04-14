<?php 

declare(strict_types=1);

namespace Lexgur\GondorGains\Script;

use Exception;

class DeleteDatabaseScript implements ScriptInterface
{
    private string $dsn;

    public function __construct(string $dsn)
    {
        $this->dsn = $dsn;
    }
    public function run(): int
    {
        $parsedDsn = parse_url($this->dsn);
        $dbPath = $parsedDsn['path'];

        if (!file_exists($dbPath)) {
            throw new Exception('Database file does not exist');
        }

        unlink($dbPath);

        return 0;
    }
}