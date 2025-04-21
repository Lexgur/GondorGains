<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Connection;
use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Model\User;
use Lexgur\GondorGains\Repository\UserModelRepository;

/**
 * @internal
 *
 * @coversNothing
 */
class DashboardWebTest extends WebTestCase
{
    private Container $container;

    private Connection $connection;

    private UserModelRepository $repository;

    public function setUp(): void
    {
        $_ENV['IS_WEB_TEST'] = 'true';

        $config = require __DIR__.'/../config.php';
        $this->container = new Container($config);
        $this->connection = $this->container->get(Connection::class);
        $this->repository = $this->container->get(UserModelRepository::class);

        parent::setUp();
    }

    public function tearDown(): void
    {
        unset($_ENV['IS_WEB_TEST']);

        session_unset();
        session_destroy();
        parent::tearDown();
    }

    public function testSuccessfulAuthentication(): void
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

        $this->assertStringContainsString("Greetings, {$username}", $dashboardOutput);
    }

    public function testFailedAuthenticationThrowsTheCorrectErrorMessage(): void
    {
        $username = 'testFailure';
        $email = 'testFailure@test.com';
        $password = 'testFailure123';
        $user = new User($username, $email, $password);
        $this->repository->save($user);

        $dashboardOutput = $this->request('GET', '/dashboard');

        $this->assertStringContainsString('You must be logged in', $dashboardOutput);
    }
}
