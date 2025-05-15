<?php

namespace Lexgur\GondorGains\Repository;

use Lexgur\GondorGains\Exception\ExerciseNotFoundException;
use Lexgur\GondorGains\Model\Exercise;
use Lexgur\GondorGains\Model\MuscleGroup;
use PDO;

class ExerciseModelRepository extends BaseRepository implements ExerciseModelRepositoryInterface
{
    public function save(Exercise $exercise): Exercise
    {
        if ($exercise->getExerciseId() === null) {
            return $this->insert($exercise);
        }

        return $this->update($exercise);
    }

    public function insert(Exercise $exercise): Exercise
    {
        $statement = $this->connection->connect()->prepare('INSERT INTO `exercises` (`name`, `muscle_group`, `description`) VALUES (:name, :muscle_group, :description)');
        $statement->bindValue(':name', $exercise->getName());
        $statement->bindValue(':muscle_group', $exercise->getMuscleGroup()->value);
        $statement->bindValue(':description', $exercise->getDescription());
        $statement->execute();
        $newId = (int)$this->connection->connect()->lastInsertId();

        return $this->fetchById($newId);
    }

    public function fetchById(int $exerciseId): ?Exercise
    {
        $statement = $this->connection->connect()->prepare('SELECT * FROM exercises WHERE id = :id');
        $statement->execute([':id' => $exerciseId]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new ExerciseNotFoundException("Exercise with id: $exerciseId does not exist");
        }
        return Exercise::create($row);
    }

    /**
     * @return array<Exercise>
     */
    public function fetchByMuscleGroup(MuscleGroup $muscleGroup): array
    {
        $statement = $this->connection->connect()->prepare('SELECT * FROM exercises WHERE muscle_group = :muscle_group');
        $statement->execute([':muscle_group' => $muscleGroup->value]);
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $exercises = [];
        foreach ($rows as $row) {
            $exercises[] = Exercise::create($row);
        }

        return $exercises;
    }

    public function assignExerciseToChallenge(int $exerciseId, ?int $challengeId): void
    {
        $database = $this->connection->connect();
        $statement = $database->prepare('
        UPDATE exercises 
        SET challenge_id = :challenge_id 
        WHERE id = :exercise_id
    ');

        $statement->bindValue(':challenge_id', $challengeId, $challengeId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $statement->bindValue(':exercise_id', $exerciseId, PDO::PARAM_INT);
        $statement->execute();
    }

    public function update(Exercise $exercise): Exercise
    {
        $statement = $this->connection->connect()->prepare('UPDATE `exercises` SET `name` = :name, `muscle_group` = :muscle_group, `description` = :description WHERE id = :id');
        $statement->bindValue(':name', $exercise->getName());
        $statement->bindValue(':muscle_group', $exercise->getMuscleGroup()->value);
        $statement->bindValue(':description', $exercise->getDescription());
        $statement->bindValue(':id', $exercise->getExerciseId());

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