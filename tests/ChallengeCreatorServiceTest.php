<?php
declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use DateTimeImmutable;
use Lexgur\GondorGains\Exception\ChallengeNotFoundException;
use Lexgur\GondorGains\Exception\ExerciseNotFoundException;
use Lexgur\GondorGains\Model\Challenge;
use Lexgur\GondorGains\Model\Exercise;
use Lexgur\GondorGains\Repository\ChallengeModelRepository;
use Lexgur\GondorGains\Repository\ExerciseModelRepository;
use Lexgur\GondorGains\Service\ChallengeCreatorService;
use Lexgur\GondorGains\Service\RandomExerciseFetcher;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Random\RandomException;

class ChallengeCreatorServiceTest extends TestCase
{
    private RandomExerciseFetcher&MockObject $fetcher;
    private ChallengeCreatorService $service;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->fetcher = $this->createMock(RandomExerciseFetcher::class);
        $this->challengeRepository = $this->createMock(ChallengeModelRepository::class);
        $this->exerciseRepository = $this->createMock(ExerciseModelRepository::class);
        $this->service = new ChallengeCreatorService(
            $this->fetcher,
            $this->exerciseRepository,
            $this->challengeRepository
        );
    }

    /**
     * @throws ExerciseNotFoundException|RandomException|Exception
     */
    public function testFetchExercisesForChallengeReturnsExercisesForSpecifiedRotation(): void
    {
        $muscleGroupRotation = 2;
        $expectedExercises = [
            $this->createStub(Exercise::class),
            $this->createStub(Exercise::class),
            $this->createStub(Exercise::class)
        ];

        $this->fetcher->expects($this->once())
            ->method('fetchRandomExercise')
            ->with($muscleGroupRotation)
            ->willReturn($expectedExercises);

        $result = $this->service->fetchExercisesForChallenge($muscleGroupRotation);

        $this->assertSame($expectedExercises, $result);
        $this->assertCount(3, $result);
    }

    /**
     * @throws ExerciseNotFoundException|RandomException|Exception
     */
    public function testFetchExercisesForChallengeUsesNextRotationWhenNoneSpecified(): void
    {
        $expectedExercises = [
            $this->createStub(Exercise::class),
            $this->createStub(Exercise::class)
        ];

        $this->fetcher->expects($this->once())
            ->method('fetchRandomExercise')
            ->with(null)
            ->willReturn($expectedExercises);

        $result = $this->service->fetchExercisesForChallenge();

        $this->assertSame($expectedExercises, $result);
        $this->assertCount(2, $result);
    }

    /**
     * @throws ExerciseNotFoundException|RandomException
     */
    public function testGenerateChallengePropagatesExceptions(): void
    {
        $this->expectException(ExerciseNotFoundException::class);

        $this->fetcher->expects($this->once())
            ->method('fetchRandomExercise')
            ->willThrowException(new ExerciseNotFoundException());
        $this->service->fetchExercisesForChallenge();
    }

    /**
     * @throws Exception
     */
    public function testAssignChallengeToExercise(): void
    {
        $challenge = $this->createMock(Challenge::class);
        $challenge->method('getChallengeId')->willReturn(42);

        $exercise1 = $this->createMock(Exercise::class);
        $exercise2 = $this->createMock(Exercise::class);

        $exercise1->expects($this->once())->method('setChallengeId')->with(42);
        $exercise2->expects($this->once())->method('setChallengeId')->with(42);

        $this->exerciseRepository
            ->expects($this->exactly(2))
            ->method('save');

        $this->service->assignChallengeToExercises($challenge, [$exercise1, $exercise2]);
    }

    /**
     * @throws Exception
     */
    public function testAssignChallengeToChallengeWithNullIdDoesNothing(): void
    {
        $challenge = $this->createMock(Challenge::class);
        $challenge->method('getChallengeId')->willReturn(null);

        $exercise = $this->createMock(Exercise::class);
        $exercise->expects($this->never())->method('setChallengeId');

        $this->exerciseRepository->expects($this->never())->method('save');

        $this->service->assignChallengeToExercises($challenge, [$exercise]);
    }

    public function testCreateChallengeReturnsChallengeWithExercisesAssigned(): void
    {
        $userId = 123;
        $challengeId = 42;

        // Create challenge with getChallengeId mocked
        $challenge = $this->getMockBuilder(Challenge::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getChallengeId'])
            ->getMock();
        $challenge->method('getChallengeId')->willReturn($challengeId);

        $this->challengeRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($challenge);

        $exercise1 = $this->createMock(Exercise::class);
        $exercise2 = $this->createMock(Exercise::class);

        $this->fetcher
            ->expects($this->once())
            ->method('fetchRandomExercise')
            ->willReturn([$exercise1, $exercise2]);

        $exercise1->expects($this->once())->method('setChallengeId')->with($challengeId);
        $exercise2->expects($this->once())->method('setChallengeId')->with($challengeId);

        $this->exerciseRepository
            ->expects($this->exactly(2))
            ->method('save');

        $result = $this->service->createChallenge($userId);

        $this->assertSame($challenge, $result);
    }


    public function testCreateChallengeForUserThrowsWhenChallengeIdIsNull(): void
    {
        $challengeWithoutId = $this->createMock(Challenge::class);
        $challengeWithoutId->method('getChallengeId')->willReturn(null);

        $this->challengeRepository
            ->expects($this->once())
            ->method('save')
            ->willReturn($challengeWithoutId);

        $this->expectException(ChallengeNotFoundException::class);

        $this->service->createChallengeForUser(1);
    }

    public function testAssignChallengeToExercisesAssignsAndSavesOnlyNonNullExercises(): void
    {
        $challenge = $this->createMock(Challenge::class);
        $challenge->method('getChallengeId')->willReturn(55);

        $exercise1 = $this->createMock(Exercise::class);
        $exercise2 = null;
        $exercise3 = $this->createMock(Exercise::class);

        $exercise1->expects($this->once())->method('setChallengeId')->with(55);
        $exercise3->expects($this->once())->method('setChallengeId')->with(55);

        $this->exerciseRepository
            ->expects($this->exactly(2))
            ->method('save')
            ->with($this->logicalOr($exercise1, $exercise3));

        $this->service->assignChallengeToExercises($challenge, [$exercise1, $exercise2, $exercise3]);
    }
}