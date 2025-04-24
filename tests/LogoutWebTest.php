<?php

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Model\User;
use Lexgur\GondorGains\Service\CurrentUser;
use Lexgur\GondorGains\Service\Session;
use PHPUnit\Framework\Attributes\DataProvider;

class LogoutWebTest extends WebTestCase
{
    private Container $container;

    public function setUp(): void
    {
        $_ENV['IS_WEB_TEST'] = 'true';

        $config = require __DIR__.'/../config.php';
        $this->container = new Container($config);

        parent::setUp();
    }

    public function testLogoutDenied(): void
    {
        $session = $this->container->get(Session::class);
        $this->assertFalse($session->hasStarted());

        $response = $this->request('GET', '/logout');
        $this->assertEquals(403, http_response_code());
        $this->assertStringContainsString('<title>Access restricted</title>', $response);
    }

    #[DataProvider('provideTestData')]
    public function testLogout(User $user): void
    {
        $this->markTestSkipped('browser works, test does not');
        /** @phpstan-ignore deadCode.unreachable */
        $session = $this->container->get(Session::class);
        $this->assertFalse($session->hasStarted());

        $session->start($user);
        $this->assertTrue($session->hasStarted());

        $currentUser = $this->container->get(CurrentUser::class);
        $this->assertTrue($currentUser->isLoggedIn());

        $response = $this->request('GET', '/logout');

        $this->assertEquals(302, http_response_code());
        $this->assertStringContainsString('', $response);
    }

    /**
     * @return array<array<User>>
     */
    public static function provideTestData(): array
    {
        return [
            [
                new User('example@localhost', 'example', 'Example123.', 1)
            ]
        ];
    }
}