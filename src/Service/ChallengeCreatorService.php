<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Service;

use Lexgur\GondorGains\Exception\ChallengeNotFoundException;
use Lexgur\GondorGains\Model\Challenge;
use Lexgur\GondorGains\Model\Exercise;
use Lexgur\GondorGains\Repository\ChallengeModelRepository;
use Lexgur\GondorGains\Repository\ExerciseModelRepository;
use Random\RandomException;

readonly class ChallengeCreatorService
{
    public function __construct(
        private RandomExerciseFetcher $fetcher,
        private ExerciseModelRepository $exerciseRepository,
        private ChallengeModelRepository $challengeRepository
    ) {}

    /**
     * @throws RandomException
     */
    public function createChallenge(int $userId, ?int $muscleGroupRotation = null): Challenge
    {
        $challenge = $this->createChallengeForUser($userId);

        $exercises = array_filter(
            $this->fetchExercisesForChallenge($muscleGroupRotation),
            fn ($exercise) => $exercise instanceof Exercise
        );

        $this->assignChallengeToExercises($challenge, $exercises);

        return $challenge;
    }

    /**
     * @return (null|Exercise)[]
     *
     * @throws RandomException
     */
    public function fetchExercisesForChallenge(?int $muscleGroupRotation = null): array
    {
        return $this->fetcher->fetchRandomExercise($muscleGroupRotation);
    }

    public function createChallengeForUser(int $userId): Challenge
    {
        $challenge = new Challenge(
            userId: $userId,
            startedAt: new \DateTimeImmutable()
        );

        return $this->challengeRepository->save($challenge);
    }

    /**
     * @param (Exercise)[] $exercises
     */
    public function assignChallengeToExercises(Challenge $challenge, array $exercises): void
    {
        $challengeId = $challenge->getChallengeId();

        foreach ($exercises as $exercise) {
            $exercise->setChallengeId($challengeId);
            $this->exerciseRepository->save($exercise);
        }
    }
}
