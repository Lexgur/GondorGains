<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Exception\UserNotFoundException;
use Lexgur\GondorGains\Repository\UserModelRepository;
use Lexgur\GondorGains\Service\CurrentUser;
use Lexgur\GondorGains\Service\PasswordVerifier;
use Lexgur\GondorGains\Service\Session;
use Lexgur\GondorGains\TemplateProvider;
use Lexgur\GondorGains\Validation\PasswordValidator;

#[Path('/login')]
class LoginUserController extends AbstractController
{
    private UserModelRepository $repository;

    private PasswordValidator $passwordValidator;

    private Session $session;

    private CurrentUser $currentUser;

    public function __construct(UserModelRepository $repository, TemplateProvider $templateProvider, PasswordValidator $passwordValidator, Session $session, CurrentUser $currentUser)
    {
        parent::__construct($templateProvider);
        $this->repository = $repository;
        $this->passwordValidator = $passwordValidator;
        $this->session = $session;
        $this->currentUser = $currentUser;
    }

    public function __invoke(): string
    {
        if ($this->currentUser->isLoggedIn()) {
            $this->redirect('/dashboard');
        }

        if ($this->isPostRequest()) {
            try {
                $data = $_POST;
                $password = $data['password'];
                $email = $data['email'];

                $this->passwordValidator->validate($password);
                $registeredUser = $this->repository->findByEmail($email);

                PasswordVerifier::verify($password, $registeredUser->getUserPassword());

                $this->session->start($registeredUser);
                $this->redirect('/dashboard');
            } catch (UserNotFoundException) {
                $this->redirect('/register');
            } catch (\Throwable) {
                return $this->render('login.html.twig', [
                    'error' => 'Incorrect user credentials',
                ]);
            }
        }

        return $this->render('login.html.twig', []);
    }
}
