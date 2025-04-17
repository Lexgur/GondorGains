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

    public function testBadRequestException(): void
    {
        $_SERVER['REQUEST_URI'] = '/test/bad-request';

        ob_start();
        $this->app->run();
        $output = ob_get_clean();

        $this->assertStringContainsString('Please check your information and try again.', $output);
        $this->assertStringContainsString('400', $output);
    }

    public function testUnauthorizedException(): void
    {
        $_SERVER['REQUEST_URI'] = '/test/unauthorized';

        ob_start();
        $this->app->run();
        $output = ob_get_clean();

        $this->assertStringContainsString('Please sign in', $output);
        $this->assertStringContainsString('401', $output);
    }

    public function testForbiddenException(): void
    {
        $_SERVER['REQUEST_URI'] = '/test/forbidden';

        ob_start();
        $this->app->run();
        $output = ob_get_clean();

        $this->assertStringContainsString('Access restricted', $output);
        $this->assertStringContainsString('403', $output);
    }

    public function testNotFoundException(): void
    {
        $_SERVER['REQUEST_URI'] = '/test/not-found';

        ob_start();
        $this->app->run();
        $output = ob_get_clean();

        $this->assertStringContainsString('Page not found', $output);
        $this->assertStringContainsString('404', $output);
    }
}

