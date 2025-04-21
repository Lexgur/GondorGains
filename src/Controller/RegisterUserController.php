<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Model\User;
use Lexgur\GondorGains\Repository\UserModelRepository;
use Lexgur\GondorGains\Service\PasswordHasher;
use Lexgur\GondorGains\TemplateProvider;
use Lexgur\GondorGains\Validation\PasswordValidator;
use Lexgur\GondorGains\Validation\UserModelValidator;

#[Path('/register')]
class RegisterUserController extends AbstractController
{
    private UserModelValidator $validator;

    private UserModelRepository $repository;

    private PasswordValidator $passwordValidator;

    public function __construct(UserModelValidator $validator, UserModelRepository $repository, TemplateProvider $templateProvider, PasswordValidator $passwordValidator)
    {
        parent::__construct($templateProvider);
        $this->validator = $validator;
        $this->repository = $repository;
        $this->passwordValidator = $passwordValidator;
    }

    public function __invoke(): string
    {
        if ($this->isPostRequest()) {
            try {
                $data = $_POST;
                $password = $data['password'];
                $this->passwordValidator->validate($password);
                $user = User::create($data);
                $this->validator->validate($user);

                $userPassword = $user->getUserPassword();
                $hashedPassword = PasswordHasher::hash($userPassword);
                $user->setUserPassword($hashedPassword);
                $this->repository->save($user);

                return $this->render('login.html.twig', [
                    'success' => 'Registration was successful,try and log in',
                ]);
            } catch (\PDOException $e) {

                return $this->render('error.html.twig', [
                    'default' => [
                        'code' => 500,
                        'title' => "We're having some trouble",
                        'message' => 'Our team has been notified. Please try again later.',
                    ],
                ]);
            } catch (\Throwable $e) {

                return $this->render('register.html.twig', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $this->render('register.html.twig', []);
    }
}