<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Service;

use Lexgur\GondorGains\Model\User;

class Session
{
    private bool $sessionStarted = false;

    public function __construct()
    {
        if ($_COOKIE['PHPSESSID']) {
            session_id($_COOKIE['PHPSESSID']);
            $status = session_start();
            $this->sessionStarted = $status;
        }
    }

    public function start(User $user): bool
    {
        $status = session_start();
        $this->sessionStarted = $status;
        $_SESSION['id'] = $user->getUserId();
        $_SESSION['user'] = $user;

        return $this->sessionStarted;
    }

    public function hasStarted(): bool
    {
        return $this->sessionStarted;
    }

    public function destroy(): bool
    {
        setcookie('PHPSESSID', '', time() - 3600);
        $_SESSION = [];
        $status = session_destroy();
        $this->sessionStarted = !$status;

        return $status;
    }
}