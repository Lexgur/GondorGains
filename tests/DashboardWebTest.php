<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\TemplateProvider;

class DashboardWebTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $config = require __DIR__ . '/../config.php';
        $config['controllerDir'] = __DIR__ . '/../src/Controller';
        $container = new Container($config);
        $this->templateProvider = $container->get(TemplateProvider::class);
    }

    public function testSuccessfulRender(): void
    {
        $_SESSION['id'] = 1;
        $output = $this->request('GET', '/dashboard');

        $this->assertStringContainsString('Greetings, gondorian', $output);
    }
}
