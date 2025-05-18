<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Service;

use Lexgur\GondorGains\Exception\ExerciseNotFoundException;
use Lexgur\GondorGains\Model\Exercise;
use Random\RandomException;

class ChallengeCreatorService
{
    public function __construct(private RandomExerciseFetcher $fetcher) {}

    /**
     * @return Exercise[]|null[]
     * @throws ExerciseNotFoundException
     * @throws RandomException
     */
    public function generateChallenge(?int $rotation = null): array
    {
        return $this->fetcher->fetchRandomExercise($rotation);
    }
}