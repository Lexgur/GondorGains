<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Database;

use PDO;
use PDOException;

class Connection
{
    private string $dsn;
    private ?\PDO $pdo = null;

    /**
     * @var array<string, mixed>
     */
    private array $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    public function __construct(string $dsn)
    {
        $this->dsn = $dsn;
    }

    public function connect(): \PDO
    {
        if (null === $this->pdo) {
            try {
                $this->pdo = new PDO($this->dsn, null, null, $this->options);
            } catch (PDOException $e) {
                throw new PDOException('Database connection failed: '.$e->getMessage(), 0, $e);
            }
        }

        return $this->pdo;
    }
}
