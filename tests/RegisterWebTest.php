<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Connection;
use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Exception\UserNotFoundException;
use Lexgur\GondorGains\Model\User;
use Lexgur\GondorGains\Repository\UserModelRepository;
use PHPUnit\Framework\Attributes\DataProvider;

class RegisterWebTest extends WebTestCase
{
    private Container $container;

    private UserModelRepository $userModelRepository;

    private Connection $database;

    public function setUp(): void
    {
        $_ENV['IS_WEB_TEST'] = 'true';

        $config = require __DIR__.'/../config.php';
        $this->container = new Container($config);
        $this->userModelRepository = $this->container->get(UserModelRepository::class);
        $this->database = $this->container->get(Connection::class);

        parent::setUp();
    }

    public function tearDown(): void
    {
        $this->database->connect()->exec('DELETE FROM users');
    }

    public function testRegisterPageAccessible(): void
    {
        $response = $this->request('GET', '/register');
        $this->assertEquals(200, http_response_code());
        $this->assertStringContainsString('Register', $response);
    }

    /**
     * @throws UserNotFoundException
     */
    #[DataProvider('provideUserData')]
    public function testSuccessfulRegistration(User $user): void
    {
        $data = [
            'email' => $user->getUserEmail(),
            'username' => $user->getUsername(),
            'password' => 'Example123.',
        ];

        $response = $this->request('POST', '/register', $data);

        $this->assertEquals(200, http_response_code());
        $this->assertStringContainsString('Registration was successful', $response);

        $savedUser = $this->userModelRepository->findByEmail($user->getUserEmail());

        $this->assertNotNull($savedUser);
        $this->assertEquals($user->getUserEmail(), $savedUser->getUserEmail());
    }

    /**
     * @param array<string, string> $data
     */
    #[DataProvider('provideInvalidUserData')]
    public function testInvalidRegistrationData(array $data, string $errorMessage): void
    {
        $response = $this->request('POST', '/register', $data);

        $this->assertEquals(200, http_response_code());
        $this->assertStringContainsString($errorMessage, $response);
    }

    /**
     * @return array<array<User>>
     */
    public static function provideUserData(): array
    {
        return [
            [
                new User('example@localhost.example', 'example', 'Example123.', 1),
            ],
        ];
    }

    /**
     * @return array<array{array<string, string>, string}>
     */
    public static function provideInvalidUserData(): array
    {
        return [
            [
                [
                    'email' => '',
                    'username' => 'test',
                    'password' => '123',
                ],
                'Password must be at least 8 characters long',
            ],
            [
                [
                    'email' => 'invalid-email',
                    'username' => 'test',
                    'password' => 'ValidPassword123!',
                ],
                'Invalid email format',
            ],
        ];
    }
}
