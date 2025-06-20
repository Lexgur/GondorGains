<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Connection;
use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Exception\ChallengeNotFoundException;
use Lexgur\GondorGains\Model\Challenge;
use Lexgur\GondorGains\Repository\ChallengeModelRepository;
use Lexgur\GondorGains\Script\RunMigrationsScript;
use PHPUnit\Framework\TestCase;

class ChallengeModelRepositoryTest extends TestCase
{
    private Connection $database;

    private ChallengeModelRepository $repository;

    public function setUp(): void
    {
        $config = require __DIR__.'/../config.php';
        $container = new Container($config);
        $this->database = $container->get(Connection::class);

        $runMigrations = $container->get(RunMigrationsScript::class);
        $runMigrations->run();

        $this->database->connect()->exec('DELETE FROM challenges');

        $this->repository = $container->get(ChallengeModelRepository::class);
    }

    public function testSuccessfulInsertReturnsChallengeProvided(): void
    {
        $challenge = new Challenge(
            userId: 1,
            startedAt: new \DateTime('2025-01-01'),
            completedAt: new \DateTime('2025-01-02')
        );
        $insertedChallenge = $this->repository->insert($challenge);

        $this->assertNotNull($insertedChallenge->getChallengeId());
        $this->assertEquals(1, $insertedChallenge->getUserId());
        $this->assertEquals(new \DateTime('2025-01-01'), $insertedChallenge->getStartedAt());
        $this->assertEquals(new \DateTime('2025-01-02'), $insertedChallenge->getCompletedAt());
    }

    public function testFetchByIdReturnsChallengeWhenValidIdExists(): void
    {
        $challenge = new Challenge(
            userId: 1,
            startedAt: new \DateTime('2025-01-01'),
            completedAt: new \DateTime('2025-01-02')
        );
        $this->repository->insert($challenge);
        $challengeId = (int) $this->database->connect()->lastInsertId();
        $existingChallenge = $this->repository->fetchById($challengeId);

        $this->assertEquals($challengeId, $existingChallenge->getChallengeId());
    }

    public function testFetchAllChallengesReturnsAllChallenges(): void
    {
        $challenge = new Challenge(
            userId: 1,
            startedAt: new \DateTime('2025-01-01'),
            completedAt: new \DateTime('2025-01-02')
        );
        $this->repository->insert($challenge);

        $challenge2 = new Challenge(
            userId: 1,
            startedAt: new \DateTime('2025-01-02'),
            completedAt: new \DateTime('2025-01-03')
        );
        $this->repository->insert($challenge2);

        $challenge3 = new Challenge(
            userId: 1,
            startedAt: new \DateTime('2025-01-03'),
            completedAt: new \DateTime('2025-01-04')
        );
        $this->repository->insert($challenge3);

        $challenge4 = new Challenge(
            userId: 1,
            startedAt: new \DateTime('2025-01-04'),
            completedAt: new \DateTime('2025-01-05')
        );
        $this->repository->insert($challenge4);

        $allChallenges = $this->repository->fetchAllChallenges();

        $this->assertNotEmpty($allChallenges);
        $this->assertCount(4, $allChallenges);
    }

    public function testFetchAllChallengesWithoutChallengesReturnsEmptyArray(): void
    {
        $allChallenges = $this->repository->fetchAllChallenges();

        $this->assertEquals([], $allChallenges);
        $this->assertCount(0, $allChallenges);
    }

    public function testFetchByIdThrowsChallengeNotFoundExceptionWhenChallengeDoesNotExist(): void
    {
        $this->expectException(ChallengeNotFoundException::class);

        $challenge = new Challenge(
            userId: 1,
            startedAt: new \DateTime('2025-01-01'),
            completedAt: new \DateTime('2025-01-02')
        );
        $this->repository->insert($challenge);
        $this->repository->fetchById(9999);
    }

    public function testSuccessfulInsertionOfMultipleChallenges(): void
    {
        $challenge1 = new Challenge(
            userId: 1,
            startedAt: new \DateTime('2025-01-01'),
            completedAt: new \DateTime('2025-01-02')
        );
        $insertedChallenge1 = $this->repository->insert($challenge1);

        $challenge2 = new Challenge(
            userId: 2,
            startedAt: new \DateTime('2025-02-01'),
            completedAt: new \DateTime('2025-02-02')
        );
        $insertedChallenge2 = $this->repository->insert($challenge2);

        $this->assertNotNull($insertedChallenge1->getChallengeId());
        $this->assertNotNull($insertedChallenge2->getChallengeId());

        $this->assertEquals($insertedChallenge1->getUserId(), $challenge1->getUserId());
        $this->assertEquals($insertedChallenge2->getUserId(), $challenge2->getUserId());
        $this->assertEquals($insertedChallenge1->getStartedAt(), $challenge1->getStartedAt());
        $this->assertEquals($insertedChallenge2->getStartedAt(), $challenge2->getStartedAt());
        $this->assertEquals($insertedChallenge1->getCompletedAt(), $challenge1->getCompletedAt());
        $this->assertEquals($insertedChallenge2->getCompletedAt(), $challenge2->getCompletedAt());

        $this->assertNotEquals($insertedChallenge1->getChallengeId(), $insertedChallenge2->getChallengeId());
    }

    public function testUpdateSuccessfullyUpdatesChallengesAttributes(): void
    {
        $challenge = new Challenge(
            userId: 1,
            startedAt: new \DateTime('2025-01-01'),
            completedAt: new \DateTime('2025-01-02')
        );
        $savedChallenge = $this->repository->save($challenge);

        $savedChallenge->setCompletedAt(new \DateTime('2025-01-03'));
        $updatedChallenge = $this->repository->save($savedChallenge);

        $this->assertNotNull($savedChallenge->getChallengeId());
        $this->assertEquals(new \DateTime('2025-01-03'), $updatedChallenge->getCompletedAt());
    }

    public function testSuccessfulChallengeDeletion(): void
    {
        $this->expectException(ChallengeNotFoundException::class);

        $challenge = new Challenge(
            userId: 1,
            startedAt: new \DateTime('2025-01-01'),
            completedAt: new \DateTime('2025-01-02')
        );

        $insertedChallenge = $this->repository->insert($challenge);
        $challengeId = $insertedChallenge->getChallengeId();

        $this->repository->delete($challengeId);
        $this->repository->fetchById($challengeId);
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function testFindActiveChallengeByUserIdReturnsActiveChallenge(): void
    {
        $completedChallenge = new Challenge(
            userId: 1,
            startedAt: new \DateTime('2025-01-01'),
            completedAt: new \DateTime('2025-01-02')
        );
        $this->repository->insert($completedChallenge);

        $activeChallenge = new Challenge(
            userId: 1,
            startedAt: new \DateTime('2025-02-01'),
            completedAt: null
        );
        $insertedActiveChallenge = $this->repository->insert($activeChallenge);

        $otherUserActiveChallenge = new Challenge(
            userId: 2,
            startedAt: new \DateTime('2025-03-01'),
            completedAt: null
        );
        $this->repository->insert($otherUserActiveChallenge);

        $foundChallenge = $this->repository->findActiveChallengeByUserId(1);
        $foundOtherChallenge = $this->repository->findActiveChallengeByUserId(2);

        $this->assertNotNull($foundChallenge);
        $this->assertEquals($insertedActiveChallenge->getChallengeId(), $foundChallenge->getChallengeId());
        $this->assertNull($foundChallenge->getCompletedAt());

        $this->assertNotNull($foundOtherChallenge);
        $this->assertNull($foundOtherChallenge->getCompletedAt());
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function testFindActiveChallengeByUserIdReturnsNullWhenNoActiveChallenge(): void
    {
        $result = $this->repository->findActiveChallengeByUserId(9999);
        $this->assertNull($result);
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function testFindActiveChallengeByUserIdReturnsNullWhenUserIdIsNull(): void
    {
        $this->assertNull($this->repository->findActiveChallengeByUserId(null));
    }
}