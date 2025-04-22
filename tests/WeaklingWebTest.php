<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Model\User;
use Lexgur\GondorGains\Repository\UserModelRepository;

class WeaklingWebTest extends WebTestCase
{
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

        $dashboardOutput = $this->request('GET', '/weakling');

        $this->assertStringContainsString("Weakling", $dashboardOutput['output']);
        $this->assertEquals(200, $dashboardOutput['status']);
    }

    public function testAnonymousAccessDenied(): void
    {
        $dashboardOutput = $this->request('GET', '/weakling');

        $this->assertStringContainsString("Access restricted", $dashboardOutput['output']);
        $this->assertEquals(403, $dashboardOutput['status']);
    }
}
