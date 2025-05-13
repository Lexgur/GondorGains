<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Model;

use DateTimeInterface;

class Challenge
{
    private ?int $challengeId;

    private int $userId;

    private DateTimeInterface $startedAt;

    private ?DateTimeInterface $completedAt;

    public function __construct(int $userId, DateTimeInterface $startedAt, ?DateTimeInterface $completedAt = null, ?int $challengeId = null)
    {
        $this->challengeId = $challengeId;
        $this->userId = $userId;
        $this->startedAt = $startedAt;
        $this->completedAt = $completedAt;
    }

    public function getChallengeId(): ?int
    {
        return $this->challengeId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getStartedAt(): DateTimeInterface
    {
        return $this->startedAt;
    }

    public function getCompletedAt(): ?DateTimeInterface
    {
        return $this->completedAt;
    }

    public function setCompletedAt(DateTimeInterface $completedAt): void
    {
        $this->completedAt = $completedAt;
    }

    /**
     * @param array{
     *     user_id: int,
     *     started_at: DateTimeInterface|null,
     *     completed_at: DateTimeInterface|null,
     *     id?: int|null
     * } $data
     */
    public static function create(array $data): Challenge
    {
        return new Challenge(
            userId: $data['user_id'],
            startedAt: $data['started_at'],
            completedAt: $data['completed_at'],
            challengeId: $data['id'] ?? null
        );
    }
}