<?php

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Service\Session;

class LogoutWebTest extends WebTestCase
{
    private Container $container;

    public function setUp(): void
    {
        $_ENV['IS_WEB_TEST'] = 'true';

        $config = require __DIR__ . '/../config.php';
        $this->container = new Container($config);

        parent::setUp();
    }

    public function testLogoutDenied(): void
    {
        $session = $this->container->get(Session::class);
        $this->assertFalse($session->hasStarted());

        $response = $this->request('GET', '/logout');
        $this->assertEquals(403, http_response_code());
        $this->assertStringContainsString('<title>Access restricted - Gondor Gains</title>', $response);
    }
}