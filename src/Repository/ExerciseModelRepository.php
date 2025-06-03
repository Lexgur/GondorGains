<?php

namespace Lexgur\GondorGains\Repository;

use Lexgur\GondorGains\Exception\ExerciseNotFoundException;
use Lexgur\GondorGains\Model\Exercise;
use Lexgur\GondorGains\Model\MuscleGroup;

class ExerciseModelRepository extends BaseRepository implements ExerciseModelRepositoryInterface
{
    public function save(Exercise $exercise): Exercise
    {
        if (null === $exercise->getExerciseId()) {
            return $this->insert($exercise);
        }

        return $this->update($exercise);
    }

    public function insert(Exercise $exercise): Exercise
    {
        $statement = $this->connection->connect()->prepare(
            'INSERT INTO `exercises` (`name`, `muscle_group`, `description`, `challenge_id`) VALUES (:name, :muscle_group, :description, :challenge_id)'
        );
        $statement->bindValue(':name', $exercise->getName());
        $statement->bindValue(':muscle_group', $exercise->getMuscleGroup()->value);
        $statement->bindValue(':description', $exercise->getDescription());
        $statement->bindValue(':challenge_id', $exercise->getChallengeId());
        $statement->execute();
        $newId = (int) $this->connection->connect()->lastInsertId();

        return $this->fetchById($newId);
    }

    public function fetchById(int $exerciseId): ?Exercise
    {
        $statement = $this->connection->connect()->prepare('SELECT * FROM exercises WHERE id = :id');
        $statement->execute([':id' => $exerciseId]);
        $row = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            throw new ExerciseNotFoundException("Exercise with id: {$exerciseId} does not exist");
        }

        return Exercise::create($row);
    }

    /** @return array<Exercise> */
    public function fetchByChallengeId(int $challengeId): array
    {
        $stmt = $this->connection->connect()->prepare('SELECT * FROM exercises WHERE challenge_id = :challenge_id');
        $stmt->execute([':challenge_id' => $challengeId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(fn ($row) => Exercise::create($row), $rows);
    }

    /**
     * @return array<Exercise>
     */
    public function fetchByMuscleGroup(MuscleGroup $muscleGroup): array
    {
        $statement = $this->connection->connect()->prepare('SELECT * FROM exercises WHERE muscle_group = :muscle_group');
        $statement->execute([':muscle_group' => $muscleGroup->value]);
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);

        $exercises = [];
        foreach ($rows as $row) {
            $exercises[] = Exercise::create($row);
        }

        return $exercises;
    }

    public function update(Exercise $exercise): Exercise
    {
        $statement = $this->connection->connect()->prepare('UPDATE `exercises` SET `name` = :name, `muscle_group` = :muscle_group, `description` = :description, `challenge_id` = :challenge_id WHERE id = :id');
        $statement->bindValue(':name', $exercise->getName());
        $statement->bindValue(':muscle_group', $exercise->getMuscleGroup()->value);
        $statement->bindValue(':description', $exercise->getDescription());
        $statement->bindValue(':id', $exercise->getExerciseId());
        $statement->bindValue(':challenge_id', $exercise->getChallengeId());

        $statement->execute();

        return $this->fetchById($exercise->getExerciseId());
    }

    public function delete(int $exerciseId): bool
    {
        $statement = $this->connection->connect()->prepare('DELETE FROM exercises WHERE id = :id');
        $statement->bindValue(':id', $exerciseId);
        $statement->execute();

        return true;
    }
}
