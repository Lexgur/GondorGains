<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Repository;

use Lexgur\GondorGains\Model\User;

interface UserModelRepositoryInterface
{
    public function save(User $user): User;

    public function insert(User $user): User;

    public function fetchById(int $userId): ?User;

    public function findByEmail(string $userEmail): ?User;

    public function update(User $user): User;

    public function delete(int $userId): bool;
}