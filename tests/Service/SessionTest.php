<?php

namespace Lexgur\GondorGains\Tests\Service;

use Lexgur\GondorGains\Model\User;
use Lexgur\GondorGains\Service\Session;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    private Session $session;

    protected function setUp(): void
    {
        $this->session = new Session();
    }

    #[DataProvider('provideTestData')]
    public function testStart(User $user): void
    {
        $this->assertFalse($this->session->hasStarted());
        $this->assertTrue($this->session->start($user));
        $this->assertEquals($user->getUserId(), $_SESSION['id']);
        $this->assertEquals($user, $_SESSION['user']);
    }

    #[DataProvider('provideTestData')]
    public function testHasStarted(User $user): void
    {
        $this->assertFalse($this->session->hasStarted());

        $this->session->start($user);

        $this->assertTrue($this->session->hasStarted());
        $this->assertEquals($user->getUserId(), $_SESSION['id']);
        $this->assertEquals($user, $_SESSION['user']);

        $this->session->destroy();

        $this->assertFalse($this->session->hasStarted());
    }

    #[DataProvider('provideTestData')]
    public function testDestroy(User $user): void
    {
        $this->session->start($user);

        $this->assertTrue($this->session->hasStarted());
        $this->assertTrue($this->session->destroy());
        $this->assertFalse($this->session->hasStarted());
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