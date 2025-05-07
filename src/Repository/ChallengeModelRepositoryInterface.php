<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Repository;

use Lexgur\GondorGains\Model\Challenge;

interface ChallengeModelRepositoryInterface
{
    public function save (Challenge $challenge): Challenge;

    public function insert (Challenge $challenge): Challenge;

    public function fetchById(int $challengeId): ?Challenge;

    public function update (Challenge $challenge): Challenge;

    public function delete (int $challengeId): bool;
}