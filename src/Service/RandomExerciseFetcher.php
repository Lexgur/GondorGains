<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Service;

use Lexgur\GondorGains\Exception\NotEnoughExercisesException;
use Lexgur\GondorGains\Model\Exercise;
use Lexgur\GondorGains\Model\MuscleGroup;
use Lexgur\GondorGains\Repository\ExerciseModelRepository;
use Random\RandomException;

class RandomExerciseFetcher
{
    public const ROTATION_1 = 1;
    public const ROTATION_2 = 2;
    public const ROTATION_3 = 3;

    private const ROTATIONS = [
        self::ROTATION_1 => [MuscleGroup::LEGS, MuscleGroup::SHOULDERS],
        self::ROTATION_2 => [MuscleGroup::CHEST, MuscleGroup::BACK, MuscleGroup::ARMS, MuscleGroup::SHOULDERS],
        self::ROTATION_3 => [MuscleGroup::CORE, MuscleGroup::BACK],
    ];

    private const MIN_EXERCISES_PER_GROUP = 2;
    private const MAX_EXERCISES_PER_GROUP = 3;

    /** @var array<int> */
    private array $rotationSequence = [];

    /** @var array<string, array<int>> */
    private array $usedExercises = [];

    public function __construct(private readonly ExerciseModelRepository $exerciseRepository)
    {
        $this->initializeRotationSequence();
    }

    private function initializeRotationSequence(): void
    {
        $this->rotationSequence = [self::ROTATION_1, self::ROTATION_2, self::ROTATION_3];
        shuffle($this->rotationSequence);
    }

    public function getNextRotation(): int
    {
        if (empty($this->rotationSequence)) {
            $this->initializeRotationSequence();
        }

        return array_shift($this->rotationSequence);
    }

    /**
     * @return array<int|MuscleGroup>
     * @throws RandomException
     */
    public function fetchRandomExerciseIds(?int $rotation = null): array
    {
        $rotation = $rotation ?? $this->getNextRotation();

        if (!isset(self::ROTATIONS[$rotation])) {
            throw new \InvalidArgumentException('Invalid rotation number');
        }

        $muscleGroups = self::ROTATIONS[$rotation];
        $exerciseIds = [];

        foreach ($muscleGroups as $muscleGroup) {
            $exercises = $this->exerciseRepository->fetchByMuscleGroup($muscleGroup);
            $availableExercises = $this->filterUsedExercises($exercises, $muscleGroup->value);

            if (count($availableExercises) < self::MIN_EXERCISES_PER_GROUP) {
                $this->usedExercises[$muscleGroup->value] = [];
                $availableExercises = $exercises;
            }

            if (count($availableExercises) < self::MIN_EXERCISES_PER_GROUP) {
                throw new NotEnoughExercisesException(
                    "Not enough exercises for muscle group {$muscleGroup->value}. " .
                    "Minimum required: " . self::MIN_EXERCISES_PER_GROUP
                );
            }

            shuffle($availableExercises);
            $numberOfExercises = random_int(
                self::MIN_EXERCISES_PER_GROUP,
                min(count($availableExercises), self::MAX_EXERCISES_PER_GROUP)
            );

            for ($i = 0; $i < $numberOfExercises; $i++) {
                $exerciseId = $availableExercises[$i]->getExerciseId();
                $exerciseIds[] = $exerciseId;
                $this->usedExercises[$muscleGroup->value][] = $exerciseId;
            }
        }

        return $exerciseIds;
    }

    /**
     * @param array<Exercise> $exercises
     * @return array<Exercise>
     */
    private function filterUsedExercises(array $exercises, string $muscleGroup): array
    {
        if (!isset($this->usedExercises[$muscleGroup])) {
            $this->usedExercises[$muscleGroup] = [];
        }

        return array_filter($exercises, function (Exercise $exercise) use ($muscleGroup) {
            return !in_array($exercise->getExerciseId(), $this->usedExercises[$muscleGroup], true);
        });
    }
}
