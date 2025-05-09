<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Connection;
use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Model\User;
use Lexgur\GondorGains\Repository\UserModelRepository;
use Lexgur\GondorGains\Service\Session;

class DashboardWebTest extends WebTestCase
{
    private UserModelRepository $repository;

    private Session $session;

    private Connection $connection;

    public function setUp(): void
    {
        $_ENV['IS_WEB_TEST'] = 'true';

        $config = require __DIR__.'/../config.php';
        $container = new Container($config);
        $this->repository = $container->get(UserModelRepository::class);
        $this->session = $container->get(Session::class);
        $this->database = $container->get(Connection::class);

        parent::setUp();
    }

    public function tearDown(): void
    {
        $this->database->connect()->exec('DELETE FROM users');
        $this->session->destroy();
        parent::tearDown();
    }

    public function testLoggedInSuccess(): void
    {
        $username = 'testSuccess';
        $email = 'testSuccess@test.com';
        $password = 'testSuccess123';
        $user = new User($username, $email, $password);
        $savedUser = $this->repository->save($user);
        $this->session->start($savedUser);

        $response = $this->request('GET', '/dashboard');
        $statusCode = http_response_code();

        $this->assertStringContainsString("Greetings, {$username}", $response);
        $this->assertEquals(200 ,$statusCode);
    }

    public function testAnonymousAccessDenied(): void
    {
        $response = $this->request('GET', '/dashboard');
        $statusCode = http_response_code();

        $this->assertStringContainsString('<title>Access restricted - Gondor Gains</title>', $response);
        $this->assertEquals(403, $statusCode);
    }
}
