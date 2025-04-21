<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Repository\UserModelRepository;
use Lexgur\GondorGains\Service\PasswordHasher;
use Lexgur\GondorGains\Service\PasswordVerifier;
use Lexgur\GondorGains\TemplateProvider;
use Lexgur\GondorGains\Validation\PasswordValidator;

#[Path('/login')]
class LoginUserController extends AbstractController
{
    private UserModelRepository $repository;

    private PasswordValidator $passwordValidator;

    public function __construct(UserModelRepository $repository, TemplateProvider $templateProvider, PasswordValidator $passwordValidator )
    {
        parent::__construct($templateProvider);
        $this->repository = $repository;
        $this->passwordValidator = $passwordValidator;
    }

    public function __invoke(): string
    {
        if ($this->isPostRequest()) {
            try {
                $data = $_POST;
                $password = $data['password'];
                $email = $data['email'];
                $this->passwordValidator->validate($password);
                PasswordHasher::hash($password);
                $registeredUser = $this->repository->findByEmail($email);
                PasswordVerifier::verify($password, $registeredUser->getUserPassword());

                session_start();
                $_SESSION['username'] = $registeredUser->getUsername();

                return $this->render('login.html.twig', [
                    'message' => "Greetings, {$registeredUser->getUsername()}, on average you have completed X of your Y quests!",
                    'quote' => '“All we have to decide is what to do with the time that is given us.” — Gandalf'
                ]);

            } catch (\Throwable) {
                return $this->render('login.html.twig', [
                    'error' => 'Invalid login credentials. Please try again.'
                ]);
            }
        }
        return $this->render('login.html.twig');
    }
}