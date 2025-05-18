<?php
declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Exception\ExerciseNotFoundException;
use Lexgur\GondorGains\Model\Exercise;
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
        $this->service = new ChallengeCreatorService($this->fetcher);
    }

    /**
     * @throws ExerciseNotFoundException|RandomException|Exception
     */
    public function testGenerateChallengeReturnsExercisesForSpecifiedRotation(): void
    {
        $rotation = 2;
        $expectedExercises = [
            $this->createStub(Exercise::class),
            $this->createStub(Exercise::class),
            $this->createStub(Exercise::class)
        ];

        $this->fetcher->expects($this->once())
            ->method('fetchRandomExercise')
            ->with($rotation)
            ->willReturn($expectedExercises);

        $result = $this->service->generateChallenge($rotation);

        $this->assertSame($expectedExercises, $result);
        $this->assertCount(3, $result);
    }

    /**
     * @throws ExerciseNotFoundException|RandomException|Exception
     */
    public function testGenerateChallengeUsesNextRotationWhenNoneSpecified(): void
    {
        $expectedExercises = [
            $this->createStub(Exercise::class),
            $this->createStub(Exercise::class)
        ];

        $this->fetcher->expects($this->once())
            ->method('fetchRandomExercise')
            ->with(null)
            ->willReturn($expectedExercises);

        $result = $this->service->generateChallenge();

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
        $this->service->generateChallenge();
    }
}