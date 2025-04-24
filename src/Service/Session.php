<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Service;

use Lexgur\GondorGains\Model\User;

class Session
{
    private bool $sessionStarted = false;

    public function start(User $user): bool
    {
            $status = session_start();
            $_SESSION['id'] = $user->getUserId();
            $_SESSION['user'] = $user;
            $this->sessionStarted = $status;

            return $this->sessionStarted;
    }

    public function hasStarted(): bool
    {
        return $this->sessionStarted;
    }

    public function destroy(): bool
    {
        $_SESSION = [];
        $status = session_destroy();
        $this->sessionStarted = !$status;

        return $status;
    }
}