<?php 

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Model\User;
use PHPUnit\Framework\TestCase;
use TypeError;

class UserTest extends TestCase

{
    public function testConstructorSetsPropertiesWhenValidArgumentsProvided(): void
    {
        $userId = 1;
        $username = "coco";
        $userPassword = "cocoIsCool";
        $userEmail = "bigboss@gmail.com";
        $user = new User($userEmail, $username, $userPassword, $userId);

        $this->assertEquals($userId, $user->getUserId());
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($userEmail, $user->getUserEmail());
        $this->assertEquals($userPassword, $user->getUserPassword());
    }

    public function testConstructorThrowsTypeErrorWhenInvalidArgumentIsProvided(): void
    {
        $this->expectException(TypeError::class);

        $userId = '11';
        $userEmail = "test@test.com";
        $username = "test";
        $userPassword = '123456';
        /** @phpstan-ignore-next-line argument.type */
        $user = new User($userEmail, $username, $userPassword, $userId);
    }
}