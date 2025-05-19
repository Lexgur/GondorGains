<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Service;

use Lexgur\GondorGains\Exception\ExerciseNotFoundException;
use Lexgur\GondorGains\Exception\NotEnoughExercisesException;
use Lexgur\GondorGains\Model\Exercise;
use Lexgur\GondorGains\Model\MuscleGroup;
use Lexgur\GondorGains\Repository\ExerciseModelRepository;
use Random\RandomException;

class RandomExerciseFetcher
{
    private const MUSCLE_GROUP_ROTATIONS = [
        1 => [MuscleGroup::LEGS, MuscleGroup::SHOULDERS],
        2 => [MuscleGroup::CHEST, MuscleGroup::BACK, MuscleGroup::ARMS, MuscleGroup::SHOULDERS],
        3 => [MuscleGroup::CORE, MuscleGroup::BACK],
    ];

    private const MIN_EXERCISES_PER_GROUP = 2;
    private const MAX_EXERCISES_PER_GROUP = 3;

    /** @var array<int> */
    private array $muscleGroupRotationSequence = [];

    /** @var array<int, array<string, array<int>>> */
    private array $usedExercises = [];

    public function __construct(private readonly ExerciseModelRepository $exerciseRepository)
    {
        $this->initializeRotationSequence();
    }

    private function initializeRotationSequence(): void
    {
        $this->muscleGroupRotationSequence = array_keys(self::MUSCLE_GROUP_ROTATIONS);
        shuffle($this->muscleGroupRotationSequence);
    }

    public function getNextRotation(): int
    {
        if (empty($this->muscleGroupRotationSequence)) {
            $this->initializeRotationSequence();
        }

        return array_shift($this->muscleGroupRotationSequence);
    }

    /**
     * @return array<Exercise|null>
     * @throws RandomException
     */
    public function fetchRandomExercise(?int $muscleGroupRotation = null): array
    {
        $muscleGroupRotation = $muscleGroupRotation ?? $this->getNextRotation();

        if (!isset(self::MUSCLE_GROUP_ROTATIONS[$muscleGroupRotation])) {
            throw new \InvalidArgumentException('Invalid rotation number');
        }

        $muscleGroups = self::MUSCLE_GROUP_ROTATIONS[$muscleGroupRotation];
        $selectedExercises = [];

        foreach ($muscleGroups as $muscleGroup) {
            $exercises = $this->exerciseRepository->fetchByMuscleGroup($muscleGroup);
            $availableExercises = $this->filterUsedExercises($exercises, $muscleGroupRotation, $muscleGroup->value);

            if (count($availableExercises) < self::MIN_EXERCISES_PER_GROUP) {
                $this->usedExercises[$muscleGroupRotation][$muscleGroup->value] = [];
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
                $exercise = $availableExercises[$i];
                $exerciseId = $exercise->getExerciseId();

                $this->usedExercises[$muscleGroupRotation][$muscleGroup->value][] = $exerciseId;
                $selectedExercises[] = $exercise;
            }
        }

        return $selectedExercises;
    }

    /**
     * @param array<Exercise> $exercises
     * @return array<Exercise>
     */
    private function filterUsedExercises(array $exercises, int $muscleGroupRotation, string $muscleGroup): array
    {
        if (!isset($this->usedExercises[$muscleGroupRotation][$muscleGroup])) {
            $this->usedExercises[$muscleGroupRotation][$muscleGroup] = [];
        }

        return array_filter($exercises, function (Exercise $exercise) use ($muscleGroupRotation, $muscleGroup) {
            return !in_array($exercise->getExerciseId(), $this->usedExercises[$muscleGroupRotation][$muscleGroup], true);
        });
    }
}
