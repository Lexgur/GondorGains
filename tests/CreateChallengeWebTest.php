<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Connection;
use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Model\User;
use Lexgur\GondorGains\Repository\UserModelRepository;
use Lexgur\GondorGains\Service\Session;

class CreateChallengeWebTest extends WebTestCase
{

    private Container $container;

    private UserModelRepository $userModelRepository;

    private Session $session;

    private Connection $database;

    public function setUp(): void
    {
        $_ENV['IS_WEB_TEST'] = 'true';

        $config = require __DIR__.'/../config.php';
        $this->container = new Container($config);
        $this->session = $this->container->get(Session::class);
        $this->database = $this->container->get(Connection::class);
        $this->userModelRepository = $this->container->get(UserModelRepository::class);

        parent::setUp();
    }

    public function tearDown(): void
    {
        $this->database->connect()->exec('DELETE FROM challenges');
        $this->session->destroy();
    }

    public function testChallengePageAccessible(): void
    {
        $this->markTestSkipped('Just like in Quests the sessions are not handled properly');
        $username = 'testSuccess';
        $email = 'testSuccess@test.com';
        $password = 'testSuccess123';
        $user = new User($username, $email, $password);
        $savedUser = $this->userModelRepository->save($user);
        $this->session->start($savedUser);

        $response = $this->request('GET', '/daily-quest/start');

        $this->assertEquals(200, http_response_code());
        $this->assertStringContainsString('List of completed quests, Gondorian', $response);
    }

    public function testAnonymousAccessDenied(): void
    {
        $response = $this->request('GET', '/daily-quest/start');
        $statusCode = http_response_code();

        $this->assertStringContainsString('<title>Access restricted - Gondor Gains</title>', $response);
        $this->assertEquals(403, $statusCode);
    }

}
