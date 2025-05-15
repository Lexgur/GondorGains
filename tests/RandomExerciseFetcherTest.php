<?php

namespace Lexgur\GondorGains\Tests;

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
     * @param int $rotation
     * @param int $minExercises
     * @param int $maxExercises
     * @return void
     * @throws RandomException
     */
    #[DataProvider('provideValidRotations')]
    public function testShouldReturnValidNumberOfExercisesForRotation(int $rotation, int $minExercises, int $maxExercises): void
    {
        $this->repository
            ->method('fetchByMuscleGroup')
            ->willReturn($this->createTestExercises(MuscleGroup::CHEST, 3));

        $exerciseIds = $this->exerciseFetcher->fetchRandomExerciseIds($rotation);

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
        $this->exerciseFetcher->fetchRandomExerciseIds(5);
    }

    public function testShouldThrowNotEnoughExercisesException(): void
    {
        $this->expectException(NotEnoughExercisesException::class);

        $this->repository
            ->method('fetchByMuscleGroup')
            ->willReturn($this->createTestExercises(MuscleGroup::CHEST, 1));

        $this->exerciseFetcher->fetchRandomExerciseIds(1);
    }

    /**
     * @group rotation
     */
    public function testShouldProvideNonRepeatingRotationSequence(): void
    {
        $this->repository
            ->method('fetchByMuscleGroup')
            ->willReturn($this->createTestExercises(MuscleGroup::CHEST, 3));

        $rotations = [];

        for ($i = 0; $i < 3; $i++) {
            $rotation = $this->exerciseFetcher->getNextRotation();
            $rotations[] = $rotation;
            $this->exerciseFetcher->fetchRandomExerciseIds($rotation);
        }

        $this->assertCount(3, array_unique($rotations));
        $this->assertEqualsCanonicalizing([1, 2, 3], $rotations);
    }

    public function testShouldReshuffleRotationSequenceAfterExhaustion(): void
    {
        $usedRotations = [];

        for ($i = 0; $i < 3; $i++) {
            $usedRotations[] = $this->exerciseFetcher->getNextRotation();
        }

        $reshuffledFirst = $this->exerciseFetcher->getNextRotation();

        $this->assertCount(3, array_unique($usedRotations));
        $this->assertContains($reshuffledFirst, [1, 2, 3]);
        $this->assertNotEmpty($reshuffledFirst);
    }

    /**
     * @group exercise_selection
     * @throws RandomException
     */
    public function testShouldResetExercisesWhenAllUsed(): void
    {
        $this->repository
            ->method('fetchByMuscleGroup')
            ->willReturn($this->createTestExercises(MuscleGroup::CHEST, 3));

        $firstCall = $this->exerciseFetcher->fetchRandomExerciseIds(1);
        $secondCall = $this->exerciseFetcher->fetchRandomExerciseIds(1);
        $thirdCall = $this->exerciseFetcher->fetchRandomExerciseIds(1);
        $onlyInts = fn($array) => array_filter($array, 'is_int');

        $ids1 = $onlyInts($firstCall);
        $ids2 = $onlyInts($secondCall);
        $ids3 = $onlyInts($thirdCall);

        $allPreviousExercises = array_merge($ids1, $ids2);
        $commonExercises = array_intersect($ids3, $allPreviousExercises);

        $this->assertGreaterThanOrEqual(self::MIN_EXERCISES_ROTATION_1, count($firstCall));
        $this->assertLessThanOrEqual(self::MAX_EXERCISES_ROTATION_1, count($firstCall));

        $this->assertGreaterThanOrEqual(self::MIN_EXERCISES_ROTATION_1, count($secondCall));
        $this->assertLessThanOrEqual(self::MAX_EXERCISES_ROTATION_1, count($secondCall));

        $this->assertGreaterThanOrEqual(self::MIN_EXERCISES_ROTATION_1, count($thirdCall));
        $this->assertLessThanOrEqual(self::MAX_EXERCISES_ROTATION_1, count($thirdCall));

        $this->assertNotEmpty($commonExercises);
    }
}

