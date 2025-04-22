<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use PHPUnit\Framework\TestCase;
use Lexgur\GondorGains\Application;

abstract class WebTestCase extends TestCase
{
    private Application $app;

    public function setUp(): void
    {
        $this->app = new Application();
    }

    /** @param array<string, mixed> $data */
    public function request(string $method, string $url, array $data = []): array
    {
        $_SERVER['REQUEST_METHOD'] = strtoupper($method);
        $_SERVER['REQUEST_URI'] = $url;

        if ($method === 'POST') {
            $_POST = $data;
        } else {
            $_GET = $data;
        }

        ob_start();
        $this->app->run();
        $output = ob_get_clean();

        $status = http_response_code();

        return [
            'output' => $output,
            'status' => $status,
        ];
    }
}