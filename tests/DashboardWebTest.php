<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Model\User;
use Lexgur\GondorGains\Repository\UserModelRepository;

class DashboardWebTest extends WebTestCase
{
    private UserModelRepository $repository;

    public function setUp(): void
    {
        $_ENV['IS_WEB_TEST'] = 'true';

        $config = require __DIR__.'/../config.php';
        $container = new Container($config);
        $this->repository = $container->get(UserModelRepository::class);

        parent::setUp();
    }

    public function tearDown(): void
    {
        session_unset();
        parent::tearDown();
    }

    public function testLoggedInSuccess(): void
    {
        $username = 'testSuccess';
        $email = 'testSuccess@test.com';
        $password = 'testSuccess123';
        $user = new User($username, $email, $password);
        $savedUser = $this->repository->save($user);

        session_start();
        $_SESSION['id'] = $savedUser->getUserId();
        session_write_close();
        $dashboardOutput = $this->request('GET', '/dashboard');
        $statusCode = http_response_code();

        $this->assertStringContainsString("Greetings, {$username}", $dashboardOutput);
        $this->assertEquals(200 ,$statusCode);
    }

    public function testAnonymousAccessDenied(): void
    {
        $dashboardOutput = $this->request('GET', '/dashboard');
        $statusCode = http_response_code();

        $this->assertStringContainsString("Access restricted", $dashboardOutput);
        $this->assertEquals(403, $statusCode);
    }
}
