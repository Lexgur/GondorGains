<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Model;

class Exercise
{
    private ?int $exerciseId = null;

    private string $name;

    private MuscleGroup $muscleGroup;

    private string $description;

    public function __construct(string $name, MuscleGroup $muscleGroup, string $description, ?int $exerciseId = null)
    {
        $this->name = $name;
        $this->muscleGroup = $muscleGroup;
        $this->description = $description;
        $this->exerciseId = $exerciseId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getMuscleGroup(): MuscleGroup
    {
        return $this->muscleGroup;
    }

    public function setMuscleGroup(MuscleGroup $muscleGroup): void
    {
        $this->muscleGroup = $muscleGroup;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getExerciseId(): ?int
    {
        return $this->exerciseId;
    }

    /**
     * @param array{
     *     name: string,
     *     muscle_group: string|int|MuscleGroup,
     *     description: string,
     *     id?: int|null
     * } $data
     */
    public static function create(array $data): Exercise
    {
        $muscleGroup = $data['muscle_group'] instanceof MuscleGroup
            ? $data['muscle_group']->value
            : $data['muscle_group'];

        return new Exercise(
            name: $data['name'],
            muscleGroup: MuscleGroup::from($muscleGroup),
            description: $data['description'],
            exerciseId: $data['id'] ?? null
        );
    }
}

