<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Connection;
use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Model\User;
use Lexgur\GondorGains\Repository\UserModelRepository;
use Lexgur\GondorGains\Service\Session;

class ViewChallengeWebTest extends WebTestCase
{

    private Container $container;

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

    public function testAnonymousAccessDenied(): void
    {
        $response = $this->request('GET', '/daily-quest/11');
        $statusCode = http_response_code();

        $this->assertStringContainsString('<title>Access restricted - Gondor Gains</title>', $response);
        $this->assertEquals(403, $statusCode);
    }
}
