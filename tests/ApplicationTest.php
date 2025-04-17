<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Application;
use PHPUnit\Framework\TestCase;
use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Router;

class ApplicationTest extends TestCase
{
    private Application $app;

    private Router $router;

    private Container $container;

    protected function setUp(): void
    {
        $config = require __DIR__ . '/../config.php';
        $this->container = new Container($config);
        $this->router = $this->container->get(Router::class);
        $this->app = $this->container->get(Application::class);

        $this->app->run();
        $this->router->registerControllers();
    }

    private function request(string $method, string $url, array $data = []): string
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
        return ob_get_clean();
    }

    public function testBadRequestException(): void
    {
        $output = $this->request('GET', '/test/bad-request');

        $this->assertStringContainsString('Please check your information and try again.', $output);
        $this->assertStringContainsString('400', $output);
    }

    public function testUnauthorizedException(): void
    {
        $output = $this->request('GET', '/test/unauthorized');

        $this->assertStringContainsString('Please sign in', $output);
        $this->assertStringContainsString('401', $output);
    }

    public function testForbiddenException(): void
    {
        $output = $this->request('GET', '/test/forbidden');

        $this->assertStringContainsString('Access restricted', $output);
        $this->assertStringContainsString('403', $output);
    }

    public function testNotFoundException(): void
    {
        $output = $this->request('GET', '/test/not-found');

        $this->assertStringContainsString('Page not found', $output);
        $this->assertStringContainsString('404', $output);
    }
}

