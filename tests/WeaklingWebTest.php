<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Model\User;
use Lexgur\GondorGains\Repository\UserModelRepository;

class WeaklingWebTest extends WebTestCase
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
        $username = 'testWeakling';
        $email = 'testWeakling@test.com';
        $password = 'testWeakling123';
        $user = new User($username, $email, $password);
        $savedUser = $this->repository->save($user);

        session_start();
        $_SESSION['id'] = $savedUser->getUserId();
        session_write_close();

        $response = $this->request('GET', '/weakling');
        $statusCode = http_response_code();

        $this->assertStringContainsString("Weakling", $response);
        $this->assertEquals(200, $statusCode);
    }

    public function testAnonymousAccessDenied(): void
    {
        $response = $this->request('GET', '/weakling');
        $statusCode = http_response_code();

        $this->assertStringContainsString('<title>Access restricted</title>', $response);
        $this->assertEquals(403,$statusCode);
    }
}
