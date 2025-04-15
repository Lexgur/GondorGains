<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use PHPUnit\Framework\TestCase;
use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Repository\UserModelRepository;
use Lexgur\GondorGains\Connection;
use Lexgur\GondorGains\Model\User;
use Lexgur\GondorGains\Exception\UserNotFoundException;
use Lexgur\GondorGains\Script\RunMigrationsScript;

class UserModelRepositoryTest extends TestCase
{
    private $database;
    private $repository;

    public function setUp(): void
    {
        $config = require __DIR__ . '/../config.php';
        $container = new Container($config);
        $this->database = $container->get(Connection::class);

        $this->database->connect()->exec('DROP TABLE IF EXISTS users');

        $runMigrations = $container->get(RunMigrationsScript::class);
        $runMigrations->run();

        $this->repository = $container->get(UserModelRepository::class);
    }

    public function testIfInsertIsSuccessful(): void
    {
        $user = new User(
            userEmail: 'dave@gmail.com',
            username: 'dave',
            userPassword: '123Em778a'
        );
        $insertedUser = $this->repository->insert($user);

        $this->assertNotNull($insertedUser->getUserId());
        $this->assertEquals($user->getUserEmail(), $insertedUser->getUserEmail());
        $this->assertEquals($user->getUsername(), $insertedUser->getUsername());
        $this->assertEquals($user->getUserPassword(), $insertedUser->getUserPassword());
    }

    public function testIfFetchByIdIsSuccessful(): void
    {

        $this->database->connect()->exec("INSERT INTO users (email, username, password) VALUES ('Test@test.com', 'test', 'User12345')");
        $userId = (int)$this->database->connect()->lastInsertId();
        $user = $this->repository->fetchById($userId);

        $this->assertEquals($userId, $user->getUserId());
    }

    public function testIfFindByEmailIsSuccessful(): void
    {

        $this->database->connect()->exec("INSERT INTO users (email, username, password) VALUES ('Test@test.com', 'test', 'User12345')");

        $user = $this->repository->findByEmail('Test@test.com');

        $this->assertEquals($user->getUserEmail(), 'Test@test.com');
    }

    public function testIfLastInsertIdWithIncorrectAttributeThrowsPDOException(): void
    {
        $this->expectException(\PDOException::class);

        $this->database->connect()->exec("INSERT INTO users (email, username, password, id) VALUES ('test@test.com', 'test', 'tesT12345', 'fail')");
        $userId = (int)$this->database->connect()->lastInsertId();
        $this->repository->fetchById($userId);
    }

    public function testIfInsertingMultipleUsersIsSuccessful(): void
    {
        $user1 = new User(
            userEmail: 'test@test.com',
            username: 'test1',
            userPassword: 'tEst1799'
        );
        $insertedUser1 = $this->repository->insert($user1);
        $user2 = new User(
            userEmail: 'test2@test.com',
            username: 'test2',
            userPassword: 'Test1799'
        );
        $insertedUser2 = $this->repository->insert($user2);

        $this->assertNotNull($insertedUser1->getUserId());
        $this->assertEquals($insertedUser1->getUserEmail(), $user1->getUserEmail());
        $this->assertEquals($insertedUser1->getUsername(), $user1->getUsername());
        $this->assertEquals($insertedUser1->getUserPassword(), $user1->getUserPassword());

        $this->assertNotNull($insertedUser2->getUserId());
        $this->assertEquals($insertedUser2->getUserEmail(), $user2->getUserEmail());
        $this->assertEquals($insertedUser2->getUsername(), $user2->getUsername());
        $this->assertEquals($insertedUser2->getUserPassword(), $user2->getUserPassword());

        $this->assertNotEquals($insertedUser1->getUserId(), $insertedUser2->getUserId());
    }

    public function testIfUpdateUserIsSuccessful(): void
    {
        $user = new User(
            userEmail: 'dave@gmail.com',
            username: 'dave',
            userPassword: '123Em778a'
        );
        $insertedUser = $this->repository->save($user);

        $insertedUser->setUserEmail('davenowmarried@gmail.com');
        $insertedUser->setUsername('davenowfree');
        $insertedUser->setUserPassword('newPassword123');

        $updatedUser = $this->repository->save($insertedUser);

        $this->assertNotNull($updatedUser->getUserId());
        $this->assertEquals('davenowmarried@gmail.com', $updatedUser->getUserEmail());
        $this->assertEquals('davenowfree', $updatedUser->getUsername());
        $this->assertEquals('newPassword123', $updatedUser->getUserPassword());
    }

    public function testIfDeleteUserIsSuccessful(): void
{
    $user = new User(
        userEmail: 'dave@gmail.com',
        username: 'dave',
        userPassword: '123Em778a'
    );
    $insertedUser = $this->repository->save($user);
    $userId = $insertedUser->getUserId();
    
    $this->repository->delete($userId);
    
    $this->expectException(UserNotFoundException::class);
    $this->repository->fetchById($userId);
}
}
