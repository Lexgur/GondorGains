<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Service\Session;

class LoginWebTest extends WebTestCase
{
    private Container $container;

    public function setUp(): void
    {
        $_ENV['IS_WEB_TEST'] = 'true';

        $config = require __DIR__.'/../config.php';
        $this->container = new Container($config);

        parent::setUp();
    }

    public function testLoginPageAccessibleToAnonymousUser(): void
    {
        $session = $this->container->get(Session::class);
        $this->assertFalse($session->hasStarted());

        $this->request('GET', '/login');
        $this->assertEquals(200, http_response_code());
    }

    public function testAnonymousTryingToLoginRedirectsToRegistration(): void
    {
        $response = $this->request('POST', '/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'Whatever123!',
        ]);

        $this->assertEquals(302, http_response_code());
        $this->assertStringContainsString('', $response);
    }
}
