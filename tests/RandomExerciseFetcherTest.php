<?php

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Exception\ExerciseNotFoundException;
use Lexgur\GondorGains\Exception\NotEnoughExercisesException;
use Lexgur\GondorGains\Model\Exercise;
use Lexgur\GondorGains\Model\MuscleGroup;
use Lexgur\GondorGains\Repository\ExerciseModelRepository;
use Lexgur\GondorGains\Service\RandomExerciseFetcher;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Random\RandomException;

class RandomExerciseFetcherTest extends TestCase
{
    private const MIN_EXERCISES_ROTATION_1 = 4;
    private const MAX_EXERCISES_ROTATION_1 = 6;
    private const MIN_EXERCISES_ROTATION_2 = 8;
    private const MAX_EXERCISES_ROTATION_2 = 12;

    private RandomExerciseFetcher $exerciseFetcher;

    /** @var ExerciseModelRepository&MockObject */
    private ExerciseModelRepository $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ExerciseModelRepository::class);
        $this->exerciseFetcher = new RandomExerciseFetcher($this->repository);
    }

    /**
     * Creates test exercises for a specific muscle group
     * @return array<Exercise>
     */
    private function createTestExercises(MuscleGroup $muscleGroup, int $count): array
    {
        $exercises = [];
        for ($i = 1; $i <= $count; $i++) {
            $exercises[] = new Exercise(
                "Exercise $i",
                $muscleGroup,
                "Description $i",
                $i
            );
        }
        return $exercises;
    }

    /**
     * @param int $muscleGroupRotation
     * @param int $minExercises
     * @param int $maxExercises
     * @return void
     * @throws RandomException
     */
    #[DataProvider('provideValidRotations')]
    public function testShouldReturnValidNumberOfExercisesForRotation(int $muscleGroupRotation, int $minExercises, int $maxExercises): void
    {
        $this->repository
            ->method('fetchByMuscleGroup')
            ->willReturn($this->createTestExercises(MuscleGroup::CHEST, 3));

        $exerciseIds = $this->exerciseFetcher->fetchRandomExercise($muscleGroupRotation);

        $this->assertGreaterThanOrEqual($minExercises, count($exerciseIds));
        $this->assertLessThanOrEqual($maxExercises, count($exerciseIds));
    }

    /**
     * @return array<string, array{int, int, int}>
     */
    public static function provideValidRotations(): array
    {
        return [
            'rotation_1' => [1, self::MIN_EXERCISES_ROTATION_1, self::MAX_EXERCISES_ROTATION_1],
            'rotation_2' => [2, self::MIN_EXERCISES_ROTATION_2, self::MAX_EXERCISES_ROTATION_2],
        ];
    }

    /**
     * @group validation
     * @throws RandomException
     */
    public function testShouldThrowExceptionForInvalidRotation(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->exerciseFetcher->fetchRandomExercise(5);
    }

    public function testShouldThrowNotEnoughExercisesException(): void
    {
        $this->expectException(NotEnoughExercisesException::class);

        $this->repository
            ->method('fetchByMuscleGroup')
            ->willReturn($this->createTestExercises(MuscleGroup::CHEST, 1));

        $this->exerciseFetcher->fetchRandomExercise(1);
    }

    /**
     * @group rotation
     */
    public function testShouldProvideNonRepeatingRotationSequence(): void
    {
        $this->repository
            ->method('fetchByMuscleGroup')
            ->willReturn($this->createTestExercises(MuscleGroup::CHEST, 3));

        $muscleGroupRotations = [];

        for ($i = 0; $i < 3; $i++) {
            $muscleGroupRotation = $this->exerciseFetcher->getNextRotation();
            $muscleGroupRotations[] = $muscleGroupRotation;
            $this->exerciseFetcher->fetchRandomExercise($muscleGroupRotation);
        }

        $this->assertCount(3, array_unique($muscleGroupRotations));
        $this->assertEqualsCanonicalizing([1, 2, 3], $muscleGroupRotations);
    }

    public function testShouldReshuffleRotationSequenceAfterExhaustion(): void
    {
        $usedMuscleGroupRotations = [];

        for ($i = 0; $i < 3; $i++) {
            $usedMuscleGroupRotations[] = $this->exerciseFetcher->getNextRotation();
        }

        $reshuffledFirst = $this->exerciseFetcher->getNextRotation();

        $this->assertCount(3, array_unique($usedMuscleGroupRotations));
        $this->assertContains($reshuffledFirst, [1, 2, 3]);
        $this->assertNotEmpty($reshuffledFirst);
    }

    /**
     * @group exercise_selection
     * @throws RandomException
     * @throws ExerciseNotFoundException
     */
    public function testShouldResetExercisesWhenAllUsed(): void
    {
        // Always return the same 3 exercises for the CHEST group
        $testExercises = $this->createTestExercises(MuscleGroup::CHEST, 3);

        $this->repository
            ->method('fetchByMuscleGroup')
            ->willReturn($testExercises);

        $this->repository
            ->method('fetchById')
            ->willReturnCallback(function (int $id) use ($testExercises) {
                foreach ($testExercises as $exercise) {
                    if ($exercise->getExerciseId() === $id) {
                        return $exercise;
                    }
                }
                return null;
            });

        // Call three times to exhaust and then trigger reset
        $firstCall = $this->exerciseFetcher->fetchRandomExercise(2);
        $secondCall = $this->exerciseFetcher->fetchRandomExercise(2);
        $thirdCall = $this->exerciseFetcher->fetchRandomExercise(2);

        $getIds = fn(array $exercises) => array_map(
            fn(Exercise $exercise) => $exercise->getExerciseId(),
            $exercises
        );

        $ids1 = $getIds($firstCall);
        $ids2 = $getIds($secondCall);
        $ids3 = $getIds($thirdCall);
        $allPreviousExercises = array_merge($ids1, $ids2);
        $commonExercises = array_intersect($ids3, $allPreviousExercises);

        $this->assertNotEmpty($ids1);
        $this->assertNotEmpty($ids2);
        $this->assertNotEmpty($ids3);
        $this->assertNotEmpty($commonExercises);
    }
}

