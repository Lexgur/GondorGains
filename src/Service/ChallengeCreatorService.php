<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Service;

use Lexgur\GondorGains\Model\Challenge;
use Lexgur\GondorGains\Model\MuscleGroup;
use Lexgur\GondorGains\Repository\ChallengeModelRepository;
use Lexgur\GondorGains\Repository\ExerciseModelRepository;
use Random\RandomException;

class ChallengeCreatorService
{
    public function __construct(
        private readonly ChallengeModelRepository $challengeRepository,
        private readonly ExerciseModelRepository $exerciseRepository,
        private readonly RandomExerciseFetcher $exerciseFetcher
    ) {
    }

    /**
     * @throws RandomException
     */
    public function createChallenge(int $userId): Challenge
    {
        $rotation = $this->exerciseFetcher->getNextRotation();
        return $this->createChallengeWithRotation($userId, $rotation);
    }

    /**
     * @throws RandomException
     */
    public function createChallengeWithRotation(int $userId, int $rotation): Challenge
    {
        $startedAt = new \DateTimeImmutable();
        $challenge = new Challenge(
            userId: $userId,
            startedAt: $startedAt
        );

        $challenge = $this->challengeRepository->save($challenge);
        $exerciseItems = $this->exerciseFetcher->fetchRandomExerciseIds($rotation);

        foreach ($exerciseItems as $item) {
            if (is_int($item)) {
                $this->exerciseRepository->assignExerciseToChallenge(
                    exerciseId: $item,
                    challengeId: $challenge->getChallengeId()
                );
            } elseif ($item instanceof MuscleGroup) {
                $exercises = $this->exerciseRepository->fetchByMuscleGroup($item);
                if (!empty($exercises)) {
                    $actualExerciseId = $exercises[0]->getExerciseId();
                    $this->exerciseRepository->assignExerciseToChallenge(
                        exerciseId: $actualExerciseId,
                        challengeId: $challenge->getChallengeId()
                    );
                }
            }
        }
        return $challenge;
    }

    /**
     * @return array<Challenge>
     * @throws RandomException
     */
    public function createFullRotationChallenge(int $userId): array
    {
        $challenges = [];
        $rotations = [
            RandomExerciseFetcher::ROTATION_1,
            RandomExerciseFetcher::ROTATION_2,
            RandomExerciseFetcher::ROTATION_3
        ];

        foreach ($rotations as $rotation) {
            $challenges[] = $this->createChallengeWithRotation($userId, $rotation);
        }

        return $challenges;
    }

    public function completeChallenge(Challenge $challenge): void
    {
        $challenge->setCompletedAt(new \DateTimeImmutable());
        $this->challengeRepository->save($challenge);
    }
}