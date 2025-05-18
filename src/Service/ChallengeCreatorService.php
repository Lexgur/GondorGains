<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Service;

use Lexgur\GondorGains\Exception\ChallengeNotFoundException;
use Lexgur\GondorGains\Exception\ExerciseNotFoundException;
use Lexgur\GondorGains\Model\Challenge;
use Lexgur\GondorGains\Model\Exercise;
use Lexgur\GondorGains\Repository\ChallengeModelRepository;
use Lexgur\GondorGains\Repository\ExerciseModelRepository;
use Random\RandomException;

class ChallengeCreatorService
{
    private RandomExerciseFetcher $fetcher;

    private ChallengeModelRepository $challengeRepository;

    private ExerciseModelRepository $exerciseRepository;

    public function __construct(RandomExerciseFetcher $fetcher, ExerciseModelRepository $exerciseModelRepository, ChallengeModelRepository $challengeModelRepository)
    {
        $this->fetcher = $fetcher;
        $this->challengeRepository = $challengeModelRepository;
        $this->exerciseRepository = $exerciseModelRepository;
    }

    /**
     * @param int $userId
     * @param int|null $muscleGroupRotation
     * @return Challenge
     * @throws ChallengeNotFoundException
     * @throws ExerciseNotFoundException
     * @throws RandomException
     */
    public function createChallenge(int $userId, ?int $muscleGroupRotation = null): Challenge
    {
        $challenge = $this->createChallengeForUser($userId);
        $exercises = $this->fetchExercisesForChallenge($muscleGroupRotation);
        $this->assignChallengeToExercises($challenge, $exercises);

        return $challenge;
    }

    /**
     * @return Exercise[]|null[]
     *
     * @throws ExerciseNotFoundException
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

        $savedChallenge = $this->challengeRepository->save($challenge);

        if (null === $savedChallenge->getChallengeId()) {
            throw new ChallengeNotFoundException('Failed to persist challenge.');
        }

        return $savedChallenge;
    }

    public function assignChallengeToExercises(Challenge $challenge, array $exercises): void
    {
        $challengeId = $challenge->getChallengeId();

        if ($challengeId === null) {
            return;
        }

        foreach ($exercises as $exercise) {
            if (null !== $exercise) {
                $exercise->setChallengeId($challengeId);
                $this->exerciseRepository->save($exercise);
            }
        }
    }
}
