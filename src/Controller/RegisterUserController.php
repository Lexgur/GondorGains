<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Validation\PasswordValidator;
use Lexgur\GondorGains\Model\User;
use Lexgur\GondorGains\Service\PasswordHasher;

#[Path('/register')]
class RegisterUserController extends AbstractController
{
    public function __invoke(): string
    {
        if ($this->isPostRequest()) {
            $data = $_POST;
            $password = $data['password'];
            try {
                PasswordValidator::validate($password);
                $user = User::create($data);
                $this->validator->validate($user);

                $userPassword = $user->getUserPassword();
                $hashedPassword = PasswordHasher::hash($userPassword);
                $user->setUserPassword($hashedPassword);
                $user = $this->repository->save($user);
            } catch (\Throwable $error) {
                return $this->render('register.html.twig', [
                    'error' => $error->getMessage()
                ]);
            }
        }
        return $this->render('register.html.twig');
    }
}