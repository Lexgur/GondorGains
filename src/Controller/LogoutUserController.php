<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Exception\ForbiddenException;
use Lexgur\GondorGains\Service\CurrentUser;
use Lexgur\GondorGains\Service\Session;
use Lexgur\GondorGains\TemplateProvider;

#[Path('/logout')]
class LogoutUserController extends AbstractController
{
    private CurrentUser $currentUser;
    private Session $session;

    public function __construct(CurrentUser $currentUser, TemplateProvider $templateProvider, Session $session)
    {
        parent::__construct($templateProvider);
        $this->currentUser = $currentUser;
        $this->session = $session;
    }
    public function __invoke(): string
    {
        if (!$this->currentUser->isLoggedIn()) {
            throw new ForbiddenException('User not logged in.');
        }

        $this->session->destroy();
        $this->redirect('/login');
        return 'login.html.twig';
    }
}