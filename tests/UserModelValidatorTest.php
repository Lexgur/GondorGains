<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Model\User;
use Lexgur\GondorGains\Repository\UserModelRepository;
use Lexgur\GondorGains\Validation\UserModelValidator;
use Lexgur\GondorGains\Connection;
use Lexgur\GondorGains\Container;
use PHPUnit\Framework\TestCase;

class UserModelValidatorTest extends TestCase
{
    private Connection $database;

    private UserModelRepository $repository;

    private UserModelValidator $validator;

    private Container $container;

    protected function setUp(): void
    {
        $config = require __DIR__ . '/../config.php';
        $this->container = new Container($config);
        $this->database = $this->container->get(Connection::class);
        $this->validator = $this->container->get(UserModelValidator::class);

        $this->database->connect()->exec('DELETE FROM users');
        $this->repository = $this->container->get(UserModelRepository::class);
    }

    public function testGivenValuesValidateSuccessfully(): void
    {
        $data = [
            'email' => 'david.jones@gmail.com',
            'username' => 'davidjones',
            'password' => 'david123Test',
        ];
        $user = new User(
            $data['email'],
            $data['username'],
            $data['password']
        );
        
        $isValid = $this->validator->validate($user);

        $this->assertTrue($isValid);
    }
}