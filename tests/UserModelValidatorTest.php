<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Model\User;
use Lexgur\GondorGains\Exception\EmailValidationException;
use Lexgur\GondorGains\Exception\UsernameValidationException;
use Lexgur\GondorGains\Validation\UserModelValidator;
use Lexgur\GondorGains\Container;
use PHPUnit\Framework\TestCase;

class UserModelValidatorTest extends TestCase
{
    private UserModelValidator $validator;

    private Container $container;

    protected function setUp(): void
    {
        $config = require __DIR__ . '/../config.php';
        $this->container = new Container($config);
        $this->validator = $this->container->get(UserModelValidator::class);
    }

    public function testGivenValuesValidateSuccessfully(): void
    {
        $data = [
            'email' => 'test@test.test',
            'username' => 'testtest',
            'password' => 'test123Test',
        ];
        $user = new User(
            $data['email'],
            $data['username'],
            $data['password']
        );
        
        $this->assertTrue($this->validator->validate($user));
    }

    public function testEmptyEmailThrowsEmailValidationException(): void
    {
        $this->expectException(EmailValidationException::class);

        $data = [
            'email' => '',
            'username' => 'testtest',
            'password' => 'test123Test',
        ];
        $user = new User(
            $data['email'],
            $data['username'],
            $data['password']
        );
        
        $this->validator->validate($user);
    }

    public function testWrongEmailFormatThrowsEmailValidationException(): void
    {
        $this->expectException(EmailValidationException::class);

        $data = [
            'email' => 'test$test.test',
            'username' => 'testtest',
            'password' => 'test123Test',
        ];
        $user = new User(
            $data['email'],
            $data['username'],
            $data['password']
        );
        
        $this->assertTrue($this->validator->validate($user));
    }

    public function testEmptyUsernameThrowsUsernameValidationException(): void
    {
        $this->expectException(UsernameValidationException::class);

        $data = [
            'email' => 'test@test.test',
            'username' => '',
            'password' => 'test123Test',
        ];
        $user = new User(
            $data['email'],
            $data['username'],
            $data['password']
        );
        
        $this->validator->validate($user);
    }

    public function testNonRegexPatternUsernameValidationException(): void
    {
        $this->expectException(UsernameValidationException::class);

        $data = [
            'email' => 'test@test.test',
            'username' => 'testsdas___',
            'password' => 'test123Test',
        ];
        $user = new User(
            $data['email'],
            $data['username'],
            $data['password']
        );
        
        $this->validator->validate($user);
    }
}