<?php

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Exception\ExerciseNotFoundException;
use Lexgur\GondorGains\Exception\NotEnoughExercisesException;
use Lexgur\GondorGains\Model\Exercise;
use Lexgur\GondorGains\Model\MuscleGroup;
use Lexgur\GondorGains\Repository\ExerciseModelRepository;
use Lexgur\GondorGains\Service\RandomExerciseFetcher;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
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

    /**
     * @throws Exception
     */
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
     * @throws RandomException|ExerciseNotFoundException
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
     * @throws RandomException|ExerciseNotFoundException
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
        $testExercises = [
            MuscleGroup::CHEST->value => $this->createTestExercises(MuscleGroup::CHEST, 2),
            MuscleGroup::BACK->value => $this->createTestExercises(MuscleGroup::BACK, 2),
            MuscleGroup::ARMS->value => $this->createTestExercises(MuscleGroup::ARMS, 2),
            MuscleGroup::SHOULDERS->value => $this->createTestExercises(MuscleGroup::SHOULDERS, 2),
        ];

        $this->repository
            ->method('fetchByMuscleGroup')
            ->willReturnCallback(function (MuscleGroup $group) use ($testExercises) {
                return $testExercises[$group->value] ?? [];
            });
        $firstBatch = $this->exerciseFetcher->fetchRandomExercise(RandomExerciseFetcher::MUSCLE_GROUP_ROTATION_2);
        $secondBatch = $this->exerciseFetcher->fetchRandomExercise(RandomExerciseFetcher::MUSCLE_GROUP_ROTATION_2);

        $firstIds = array_map(fn(Exercise $e) => $e->getExerciseId(), $firstBatch);
        $secondIds = array_map(fn(Exercise $e) => $e->getExerciseId(), $secondBatch);

        $this->assertNotEmpty(
            array_intersect($firstIds, $secondIds),
            'Expected some exercises to be reused after reset'
        );
    }
}

