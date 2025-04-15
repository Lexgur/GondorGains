<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Repository;

use Lexgur\GondorGains\Model\User;
use Lexgur\GondorGains\Exception\UserNotFoundException;
use PDO;

class UserModelRepository extends BaseRepository implements UserModelRepositoryInterface

{
    public function save(User $user): User
    {
        if ($user->getUserId() === null) {
            return $this->insert($user);
        }

        return $this->update($user);
    }
    public function insert(User $user): User
    {
        try {
            $statement = $this->connection->connect()->prepare('INSERT INTO `users` (`email`, `username`, `password`) VALUES (:email, :username, :password)');
            $statement->bindValue(':email', $user->getUserEmail());
            $statement->bindValue(':username', $user->getUsername());
            $statement->bindValue(':password', $user->getUserPassword());
            $statement->execute();
        } catch (\Throwable $e) {
            throw new \Exception('Error inserting user: ' . $e->getMessage());
        }

        $newId = (int)$this->connection->connect()->lastInsertId();
        return $this->fetchById($newId);
    }

    public function fetchById(int $userId): ?User
    {
        $statement = $this->connection->connect()->prepare('SELECT * FROM users WHERE id = :id');
        $statement->execute([':id' => $userId]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new UserNotFoundException("User with id: $userId does not exist");
        }
        return User::create($row);
    }

    public function findByEmail(string $userEmail): ?User
    {
        $statement = $this->connection->connect()->prepare('SELECT * FROM users WHERE email = :email');
        $statement->execute([':email' => $userEmail]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new UserNotFoundException("User with email: $userEmail does not exist");
        }
        return User::create($row);
    }


    public function update(User $user): User
    {
        try {
            $statement = $this->connection->connect()->prepare('UPDATE `users` SET `email` = :email, `username` = :username, `password` = :password WHERE id = :id');
            $statement->bindValue(':email', $user->getUserEmail());
            $statement->bindValue(':username', $user->getUsername());
            $statement->bindValue(':password', $user->getUserPassword());
            $statement->bindValue(':id', $user->getUserId());

            $statement->execute();
        } catch (\Throwable $e) {
            throw new \Exception('Error inserting user: ' . $e->getMessage());
        }
        return $this->fetchById($user->getUserId());
    }

    public function delete(int $userId): bool
    {
        $statement = $this->connection->connect()->prepare('DELETE FROM users WHERE id = :id');
        $statement->bindValue(':id', $userId);
        $statement->execute();

        return true;
    }
}
