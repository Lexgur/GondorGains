<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Repository;

use Lexgur\GondorGains\Model\Exercise;
interface ExerciseModelRepositoryInterface
{
    public function save (Exercise $exercise): Exercise;

    public function insert (Exercise $exercise): Exercise;

    public function fetchById(int $exerciseId): ?Exercise;

    public function update (Exercise $exercise): Exercise;

    public function delete (int $exerciseId): bool;
}