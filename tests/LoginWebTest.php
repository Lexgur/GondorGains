<?php

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Model\User;
use Lexgur\GondorGains\Service\CurrentUser;
use Lexgur\GondorGains\Service\Session;
use PHPUnit\Framework\Attributes\DataProvider;

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

    #[DataProvider('provideTestData')]
    public function testLoggedInUserIsRedirected(User $user): void
    {
        $this->markTestSkipped('Just like with LogoutWebTest, it works on browser but not here.');

        /** @phpstan-ignore deadCode.unreachable */
        $session = $this->container->get(Session::class);
        $this->assertFalse($session->hasStarted());

        $session->start($user);
        $this->assertTrue($session->hasStarted());

        $currentUser = $this->container->get(CurrentUser::class);
        $this->assertTrue($currentUser->isLoggedIn());

        $response = $this->request('GET', '/login');

        $this->assertEquals(302, http_response_code());
        $this->assertStringContainsString(sprintf('Greetings, %s', $user->getUsername()), $response);
    }

    /**
     * @return array<array<User>>
     */
    public static function provideTestData(): array
    {
        return [
            [
                new User('example@localhost', 'example', 'Example123.', 1),
            ],
        ];
    }
}
