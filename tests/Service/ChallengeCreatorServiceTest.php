<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests\Service;

use Lexgur\GondorGains\Connection;
use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Exception\ChallengeNotFoundException;
use Lexgur\GondorGains\Exception\CircularDependencyException;
use Lexgur\GondorGains\Model\Exercise;
use Lexgur\GondorGains\Model\MuscleGroup;
use Lexgur\GondorGains\Repository\ChallengeModelRepository;
use Lexgur\GondorGains\Repository\ExerciseModelRepository;
use Lexgur\GondorGains\Script\RunMigrationsScript;
use Lexgur\GondorGains\Service\ChallengeCreatorService;
use Lexgur\GondorGains\Service\RandomExerciseFetcher;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Random\RandomException;

class ChallengeCreatorServiceTest extends TestCase
{
    private ChallengeModelRepository $challengeRepository;
    private ExerciseModelRepository $exerciseRepository;
    private RandomExerciseFetcher $fetcher;
    private ChallengeCreatorService $service;

    /**
     * @throws CircularDependencyException|Exception
     */
    protected function setUp(): void
    {
        $config = require __DIR__.'/../../config.php';
        $container = new Container($config);

        $container->get(RunMigrationsScript::class)->run();
        $container->get(Connection::class)->connect()->exec('DELETE FROM exercises');
        $container->get(Connection::class)->connect()->exec('DELETE FROM challenges');

        $this->exerciseRepository = $container->get(ExerciseModelRepository::class);
        $this->challengeRepository = $container->get(ChallengeModelRepository::class);
        $this->fetcher = $container->get(RandomExerciseFetcher::class);

        $this->seedExercises();

        $this->service = new ChallengeCreatorService(
            $this->fetcher,
            $this->exerciseRepository,
            $this->challengeRepository
        );
    }

    /**
     * Covers: createChallenge(), assignChallengeToExercises().
     *
     * @throws ChallengeNotFoundException
     * @throws RandomException
     */
    public function testCreateChallenge(): void
    {
        $userId = 1;
        $challenge = $this->service->createChallenge($userId, 3);

        $this->assertNotNull($challenge->getChallengeId());

        $exercises = $this->exerciseRepository->fetchByChallengeId($challenge->getChallengeId());

        $this->assertNotEmpty($exercises);
        foreach ($exercises as $exercise) {
            $this->assertEquals($challenge->getChallengeId(), $exercise->getChallengeId());
        }
    }

    /**
     * @throws RandomException
     */
    public function testFetchExercisesForChallenge(): void
    {
        $exercises = $this->service->fetchExercisesForChallenge();

        foreach ($exercises as $exercise) {
            $this->assertInstanceOf(Exercise::class, $exercise);
        }
    }

    public function testCreateChallengeForUser(): void
    {
        $userId = 123;
        $challenge = $this->service->createChallengeForUser($userId);

        $this->assertNotNull($challenge->getChallengeId());
        $this->assertEquals($userId, $challenge->getUserId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $challenge->getStartedAt());
    }

    public function testAssignChallengeToExercises(): void
    {
        $challenge = $this->service->createChallengeForUser(99);

        $allExercises = $this->exerciseRepository->fetchByMuscleGroup(MuscleGroup::cases()[0]);
        $exercises = array_slice($allExercises, 0, 3);

        $this->service->assignChallengeToExercises($challenge, $exercises);

        foreach ($exercises as $exercise) {
            $fetched = $this->exerciseRepository->fetchById($exercise->getExerciseId());
            $this->assertEquals($challenge->getChallengeId(), $fetched->getChallengeId());
        }
    }

    private function seedExercises(int $minPerGroup = 3, int $maxPerGroup = 5): void
    {
        foreach (MuscleGroup::cases() as $group) {
            for ($i = 0; $i < rand($minPerGroup, $maxPerGroup); ++$i) {
                $exercise = new Exercise("Test {$group->value} {$i}", $group, "desc {$i}");
                $this->exerciseRepository->insert($exercise);
            }
        }
    }
}
