<?php

namespace Lexgur\GondorGains\Tests\Service;

use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Model\User;
use Lexgur\GondorGains\Service\CurrentUser;
use Lexgur\GondorGains\Service\Session;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CurrentUserTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $config = require __DIR__ . '/../../config.php';
        $this->container = new Container($config);
    }

    #[DataProvider('provideTestData')]
    public function testIsLoggedIn(User $user): void
    {
        $currentUser = $this->container->get(CurrentUser::class);
        $this->assertFalse($currentUser->isLoggedIn());

        $session = $this->container->get(Session::class);
        $session->start($user);

        $this->assertTrue($currentUser->isLoggedIn());

        $session->destroy();
        $this->assertFalse($currentUser->isLoggedIn());
    }

    #[DataProvider('provideTestData')]
    public function testIsAnonymous(User $user): void
    {
        $currentUser = $this->container->get(CurrentUser::class);
        $this->assertTrue($currentUser->isAnonymous());

        $session = $this->container->get(Session::class);
        $session->start($user);

        $this->assertFalse($currentUser->isAnonymous());

        $session->destroy();
        $this->assertTrue($currentUser->isAnonymous());
    }

    #[DataProvider('provideTestData')]
    public function testGet(User $user): void
    {
        $currentUser = $this->container->get(CurrentUser::class);
        $session = $this->container->get(Session::class);
        $session->start($user);

        $this->assertTrue($currentUser->isLoggedIn());
        $this->assertEquals($currentUser->get($currentUser), $user);
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