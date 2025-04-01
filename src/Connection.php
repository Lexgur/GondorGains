<?php

declare(strict_types=1);

namespace Lexgur\GondorGains;

use PDO;

class Connection
{
    private string $dsn;
    private ?PDO $pdo = null;

    /**
     * @var array<int, false|int>
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

    public function connect(): PDO
    {
        if ($this->pdo === null) {
            $this->pdo = new PDO($this->dsn, options: $this->options);
        }

        return $this->pdo;
    }
}

