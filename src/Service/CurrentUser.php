<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Service;

use Lexgur\GondorGains\Model\User;

class CurrentUser
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function get(): User
    {
        return $_SESSION['user'];
    }

    public function isLoggedIn(): bool
    {
        return $this->session->hasStarted() && isset($_SESSION['user']) && $_SESSION['user'] instanceof User;
    }

    public function isAnonymous(): bool
    {
        return !$this->isLoggedIn();
    }

}