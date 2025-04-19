<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Repository\UserModelRepository;
use Lexgur\GondorGains\TemplateProvider;
use Lexgur\GondorGains\Validation\PasswordValidator;
use Lexgur\GondorGains\Model\User;
use Lexgur\GondorGains\Service\PasswordHasher;
use Lexgur\GondorGains\Validation\UserModelValidator;

#[Path('/register')]
class RegisterUserController extends AbstractController
{
    private UserModelValidator $validator;
    private UserModelRepository $repository;

    private PasswordValidator $passwordValidator;

    public function __construct( UserModelValidator $validator, UserModelRepository $repository, TemplateProvider $templateProvider, PasswordValidator $passwordValidator )
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