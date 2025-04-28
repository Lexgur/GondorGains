<?php

namespace Lexgur\GondorGains\Repository;

use Lexgur\GondorGains\Exception\ExerciseNotFoundException;
use Lexgur\GondorGains\Model\Exercise;
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