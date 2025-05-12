<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Model\Challenge;
use PHPUnit\Framework\TestCase;
use TypeError;

class ChallengeTest extends TestCase
{
    public function testConstructorSetsPropertiesWhenValidArgumentsProvided(): void
    {
        $challengeId = 1;
        $userId = 2;
        $startedAt = new \DateTime('2025-05-06');
        $completedAt = new \DateTime('2025-05-07');
        $challenge = new Challenge($userId, $startedAt, $completedAt, $challengeId);


        $this->assertEquals($challengeId, $challenge->getChallengeId());
        $this->assertEquals($userId, $challenge->getUserId());
        $this->assertEquals($startedAt, $challenge->getStartedAt());
        $this->assertEquals($completedAt, $challenge->getCompletedAt());
    }

    public function testSetUserIdCorrectlySetsUserId(): void
    {
        $userId = 1;
        $newUserId = 42;
        $startedAt = new \DateTime('2025-05-06');
        $completedAt = new \DateTime('2025-05-07');
        $challenge = new Challenge($userId, $startedAt, $completedAt);

        $challenge->setUserId($newUserId);

        $this->assertEquals($newUserId, $challenge->getUserId());
    }

    public function testConstructorThrowsTypeErrorWhenInvalidArgumentIsProvided(): void
    {

        $this->expectException(TypeError::class);

        $challengeId = 'BadId';
        $userId = 11;
        $startedAt = new \DateTime('2025-05-06');
        $completedAt = new \DateTime('2025-05-07');
        /** @phpstan-ignore-next-line argument.type */
        new Challenge($userId, $startedAt, $completedAt, $challengeId);
    }
}
