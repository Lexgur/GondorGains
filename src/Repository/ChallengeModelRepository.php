<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Repository;

use Lexgur\GondorGains\Exception\ChallengeNotFoundException;
use Lexgur\GondorGains\Model\Challenge;

class ChallengeModelRepository extends BaseRepository implements ChallengeModelRepositoryInterface
{
    public function save(Challenge $challenge): Challenge
    {
        if (null === $challenge->getChallengeId()) {
            return $this->insert($challenge);
        }

        return $this->update($challenge);
    }

    public function insert(Challenge $challenge): Challenge
    {
        $statement = $this->connection->connect()->prepare('INSERT INTO `challenges` (`user_id`, `started_at`, `completed_at`) VALUES (:user_id, :started_at, :completed_at)');
        $statement->bindValue(':user_id', $challenge->getUserId());
        $statement->bindValue(':started_at', $challenge->getStartedAt()->format('Y-m-d H:i:s'));
        $statement->bindValue(':completed_at', $challenge->getCompletedAt()?->format('Y-m-d H:i:s'));
        $statement->execute();

        $newId = (int) $this->connection->connect()->lastInsertId();

        return $this->fetchById($newId);
    }

    public function fetchById(int $challengeId): ?Challenge
    {
        $statement = $this->connection->connect()->prepare('SELECT * FROM `challenges` WHERE `id` = :id');
        $statement->execute([':id' => $challengeId]);
        $row = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            throw new ChallengeNotFoundException("Challenge with id: {$challengeId} does not exist");
        }

        return Challenge::create([
            'id' => (int) $row['id'],
            'user_id' => (int) $row['user_id'],
            'started_at' => new \DateTimeImmutable($row['started_at']),
            'completed_at' => $row['completed_at'] ? new \DateTimeImmutable($row['completed_at']) : null,
        ]);
    }

    /** @return Challenge[] */
    public function fetchAllChallenges(): array
    {
        $statement = $this->connection->connect()->prepare('SELECT * FROM `challenges`');
        $statement->execute();
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(function ($row) {
            return Challenge::create([
                'id' => (int) $row['id'],
                'user_id' => (int) $row['user_id'],
                'started_at' => new \DateTimeImmutable($row['started_at']),
                'completed_at' => $row['completed_at'] ? new \DateTimeImmutable($row['completed_at']) : null,
            ]);
        }, $rows);
    }

    /**
     * @throws ChallengeNotFoundException
     * @throws \Exception
     */
    public function update(Challenge $challenge): Challenge
    {
        $statement = $this->connection->connect()->prepare('UPDATE `challenges` SET `user_id` = :user_id, `started_at` = :started_at, `completed_at` = :completed_at WHERE `id` = :id');
        $statement->bindValue(':user_id', $challenge->getUserId());
        $statement->bindValue(':started_at', $challenge->getStartedAt()->format('Y-m-d H:i:s'));
        $statement->bindValue(':completed_at', $challenge->getCompletedAt()?->format('Y-m-d H:i:s'));
        $statement->bindValue(':id', $challenge->getChallengeId());

        $statement->execute();

        return $this->fetchById($challenge->getChallengeId());
    }

    public function delete(int $challengeId): bool
    {
        $statement = $this->connection->connect()->prepare('DELETE FROM `challenges` WHERE `id` = :id');
        $statement->bindValue(':id', $challengeId);
        $statement->execute();

        return true;
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function findActiveChallengeByUserId(?int $userId): ?Challenge
    {
        if (null === $userId) {
            return null;
        }

        $statement = $this->connection->connect()->prepare(
            'SELECT * FROM `challenges` 
         WHERE `user_id` = :user_id 
         AND `completed_at` IS NULL 
         ORDER BY `started_at` DESC 
         LIMIT 1'
        );

        $statement->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $statement->execute();

        $row = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return Challenge::create([
            'id' => (int) $row['id'],
            'user_id' => (int) $row['user_id'],
            'started_at' => new \DateTimeImmutable($row['started_at']),
            'completed_at' => $row['completed_at'] ? new \DateTimeImmutable($row['completed_at']) : null,
        ]);
    }
}
