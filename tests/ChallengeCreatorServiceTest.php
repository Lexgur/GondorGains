<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Model\Challenge;
use Lexgur\GondorGains\Model\Exercise;
use Lexgur\GondorGains\Model\MuscleGroup;
use Lexgur\GondorGains\Repository\ChallengeModelRepository;
use Lexgur\GondorGains\Repository\ExerciseModelRepository;
use Lexgur\GondorGains\Service\ChallengeCreatorService;
use Lexgur\GondorGains\Service\RandomExerciseFetcher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Random\RandomException;

#[CoversClass(ChallengeCreatorService::class)] class ChallengeCreatorServiceTest extends TestCase
{
    private ChallengeModelRepository&MockObject $challengeRepository;
    private ExerciseModelRepository&MockObject $exerciseRepository;
    private MockObject&RandomExerciseFetcher $exerciseFetcher;
    private ChallengeCreatorService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->challengeRepository = $this->createMock(ChallengeModelRepository::class);
        $this->exerciseRepository = $this->createMock(ExerciseModelRepository::class);
        $this->exerciseFetcher = $this->createMock(RandomExerciseFetcher::class);

        $this->service = new ChallengeCreatorService(
            $this->challengeRepository,
            $this->exerciseRepository,
            $this->exerciseFetcher
        );
    }

    /**
     * @throws Exception
     * @throws RandomException
     */
    public function testCreateChallenge(): void
    {
        $userId = 1;
        $challengeId = 5;
        $rotation = RandomExerciseFetcher::ROTATION_1;
        $exerciseIds = [10, 11, 12, 13];
        $exercises = [];
        $capturedExerciseIds = [];
        $capturedChallengeIds = [];

        // Create mock Exercises
        foreach ($exerciseIds as $id) {
            $exercise = $this->createMock(Exercise::class);
            $exercise->method('getExerciseId')->willReturn($id);
            $exercises[] = $exercise;
        }

        $muscleGroups = [
            MuscleGroup::CHEST,
            MuscleGroup::BACK,
            MuscleGroup::LEGS,
            MuscleGroup::ARMS,
        ];

        // Mocks must be defined before calling any methods
        $this->exerciseFetcher
            ->method('fetchRandomExerciseIds')
            ->with($rotation)
            ->willReturn($muscleGroups)
        ;

        $this->exerciseFetcher
            ->method('getNextRotation')
            ->willReturn($rotation)
        ;

        $challenge = new Challenge($userId, new \DateTimeImmutable());
        $reflectionProperty = new \ReflectionProperty(Challenge::class, 'challengeId');
        $reflectionProperty->setValue($challenge, $challengeId);

        $this->challengeRepository
            ->method('save')
            ->willReturnCallback(function ($challengeArg) use ($challenge) {
                return $challenge;
            })
        ;

        // Add this after the save() mock setup
        $this->exerciseRepository
            ->expects($this->exactly(count($muscleGroups)))
            ->method('assignExerciseToChallenge')
            ->willReturnCallback(function ($exerciseId, $challengeId) use (&$capturedExerciseIds, &$capturedChallengeIds) {
                $capturedExerciseIds[] = $exerciseId;
                $capturedChallengeIds[] = $challengeId;
            })
        ;

        $exerciseMap = [];

        foreach ($muscleGroups as $index => $muscleGroup) {
            $exercise = $this->createMock(Exercise::class);
            $id = 10 + $index;
            $exercise->method('getExerciseId')->willReturn($id);
            $exerciseMap[$muscleGroup->value] = [$exercise];
        }

        $this->exerciseRepository
            ->method('fetchByMuscleGroup')
            ->willReturnCallback(function ($muscleGroup) use ($exerciseMap) {
                return $exerciseMap[$muscleGroup->value] ?? [];
            })
        ;

        $result = $this->service->createChallenge($userId);

        $this->assertSame($challenge, $result);
        $this->assertSame($userId, $result->getUserId());
        $this->assertSame($challengeId, $result->getChallengeId());

        // Before the comparison, de-duplicate the captured exercise IDs
        $capturedExerciseIds = array_unique($capturedExerciseIds);

        sort($capturedExerciseIds);
        sort($exerciseIds);
        $this->assertSame($exerciseIds, $capturedExerciseIds);
        $this->assertSame(array_fill(0, count($exerciseIds), $challengeId), $capturedChallengeIds);
    }

    /**
     * @throws RandomException
     * @throws Exception
     */
    public function testCreateChallengeWithRotation(): void
    {
        $userId = 2;
        $challengeId = 6;
        $rotation = RandomExerciseFetcher::ROTATION_2;

        // Mix of muscle groups and direct exercise IDs to test both branches
        $muscleGroups = [
            MuscleGroup::CHEST,   // Will go through the if branch
            MuscleGroup::BACK,    // Will go through the if branch
            42                    // Will go through the else branch
        ];

        // These are the actual exercise IDs assigned for each muscle group
        $exerciseIds = [10, 11, 42];  // The 42 is directly from the else branch

        // Mock fetchRandomExerciseIds to return this mixed array
        $this->exerciseFetcher
            ->method('fetchRandomExerciseIds')
            ->with($rotation)
            ->willReturn($muscleGroups);

        // Create a Challenge and force set the ID
        $challenge = new Challenge($userId, new \DateTimeImmutable());
        $reflection = new \ReflectionProperty(Challenge::class, 'challengeId');
        $reflection->setValue($challenge, $challengeId);

        // Mock save to return our challenge
        $this->challengeRepository
            ->method('save')
            ->willReturn($challenge);

        // Map muscle groups to a list with one Exercise mock each (only for the real muscle groups)
        $exerciseMap = [];
        // Only create mocks for the first two items (MuscleGroup objects)
        for ($i = 0; $i < 2; $i++) {
            $muscleGroup = $muscleGroups[$i];
            $exercise = $this->createMock(Exercise::class);
            $exercise->method('getExerciseId')->willReturn($exerciseIds[$i]);
            $exerciseMap[$muscleGroup->value] = [$exercise];
        }

        // Mock fetchByMuscleGroup to return exercise mocks according to muscle group
        $this->exerciseRepository
            ->method('fetchByMuscleGroup')
            ->willReturnCallback(function ($muscleGroup) use ($exerciseMap) {
                return $exerciseMap[$muscleGroup->value] ?? [];
            });

        // Capture calls to assignExerciseToChallenge
        $capturedExerciseIds = [];
        $capturedChallengeIds = [];

        $this->exerciseRepository
            ->expects($this->exactly(count($muscleGroups)))
            ->method('assignExerciseToChallenge')
            ->willReturnCallback(function ($exerciseId, $challengeId) use (&$capturedExerciseIds, &$capturedChallengeIds) {
            $capturedExerciseIds[] = $exerciseId;
            $capturedChallengeIds[] = $challengeId;
        });

        // Act
        $result = $this->service->createChallengeWithRotation($userId, $rotation);

        // Assert
        $this->assertSame($challenge, $result);
        $this->assertSame($userId, $result->getUserId());
        $this->assertSame($challengeId, $result->getChallengeId());

        sort($capturedExerciseIds);
        sort($exerciseIds);
        $this->assertSame($exerciseIds, $capturedExerciseIds);
        $this->assertSame(array_fill(0, count($exerciseIds), $challengeId), $capturedChallengeIds);
    }

    /**
     * @throws RandomException
     * @throws Exception
     */
    public function testCreateFullRotationChallenge(): void
    {
        $userId = 3;

        $rotations = [
            RandomExerciseFetcher::ROTATION_1,
            RandomExerciseFetcher::ROTATION_2,
            RandomExerciseFetcher::ROTATION_3,
        ];

        $challenges = [];
        foreach ($rotations as $index => $rotation) {
            $challenge = new Challenge($userId, new \DateTimeImmutable());
            $reflectionProperty = new \ReflectionProperty(Challenge::class, 'challengeId');
            $reflectionProperty->setValue($challenge, 10 + $index);
            $challenges[] = $challenge;
        }

        /** @var ChallengeCreatorService&MockObject $servicePartialMock */
        $servicePartialMock = $this->getMockBuilder(ChallengeCreatorService::class)
            ->setConstructorArgs([
                $this->challengeRepository,
                $this->exerciseRepository,
                $this->exerciseFetcher,
            ])
            ->onlyMethods(['createChallengeWithRotation'])
            ->getMock()
        ;

        // Configure the partial mock
        $servicePartialMock
            ->method('createChallengeWithRotation')
            ->willReturnCallback(function ($actualUserId, $actualRotation) use ($userId, $rotations, $challenges) {
                static $callCount = 0;
                $this->assertSame($userId, $actualUserId);
                $this->assertSame($rotations[$callCount], $actualRotation);

                return $challenges[$callCount++];
            })
        ;

        $result = $servicePartialMock->createFullRotationChallenge($userId);

        $this->assertCount(3, $result);
        foreach ($rotations as $index => $rotation) {
            $this->assertSame($challenges[$index], $result[$index]);
            $this->assertSame($userId, $result[$index]->getUserId());
            $this->assertSame(10 + $index, $result[$index]->getChallengeId());
        }
    }

    /**
     * @throws Exception
     */
    public function testCompleteChallenge(): void
    {
        $userId = 4;
        $challengeId = 9;

        $challenge = new Challenge($userId, new \DateTimeImmutable());
        $reflectionProperty = new \ReflectionProperty(Challenge::class, 'challengeId');
        $reflectionProperty->setValue($challenge, $challengeId);

        $this->challengeRepository
            ->method('save')
            ->with(
                $this->callback(function ($arg) use ($challengeId, $userId) {
                    return $arg instanceof Challenge
                        && $arg->getUserId() === $userId
                        && $arg->getChallengeId() === $challengeId
                        && null !== $arg->getCompletedAt();
                })
            );

        $this->service->completeChallenge($challenge);

        $this->assertNotNull($challenge->getCompletedAt());
    }
}