<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Connection;
use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Exception\ExerciseNotFoundException;
use Lexgur\GondorGains\Model\Exercise;
use Lexgur\GondorGains\Model\MuscleGroup;
use Lexgur\GondorGains\Repository\ExerciseModelRepository;
use Lexgur\GondorGains\Script\RunMigrationsScript;
use PHPUnit\Framework\TestCase;

class ExerciseModelRepositoryTest extends TestCase
{
    private Connection $database;

    private ExerciseModelRepository $repository;

    public function setUp(): void
    {
        $config = require __DIR__ . '/../config.php';
        $container = new Container($config);
        $this->database = $container->get(Connection::class);

        $runMigrations = $container->get(RunMigrationsScript::class);
        $runMigrations->run();

        $this->database->connect()->exec('DELETE FROM exercises');

        $this->repository = $container->get(ExerciseModelRepository::class);
    }

    public function testSuccessfulInsertReturnsExerciseProvided(): void
    {
        $exercise = new Exercise(
            name: 'Tests',
            muscleGroup: MuscleGroup::CHEST,
            description: 'test'
        );
        $insertedExercise = $this->repository->insert($exercise);

        $this->assertNotNull($insertedExercise->getExerciseId());
    }

    public function testFetchByIdReturnsUserWhenValidIdExists(): void
    {
        $exercise = new Exercise(
            name: 'Tests',
            muscleGroup: MuscleGroup::CHEST,
            description: 'test'
        );
        $this->repository->insert($exercise);
        $exerciseId = (int)$this->database->connect()->lastInsertId();
        $existingExercise = $this->repository->fetchById($exerciseId);

        $this->assertEquals($exerciseId, $existingExercise->getExerciseId());
    }

    public function testFetchByIdThrowsExerciseNotFoundExceptionWhenUserDoesNotExist(): void
    {
        $this->expectException(ExerciseNotFoundException::class);

        $exercise = new Exercise(
            name: 'Tests',
            muscleGroup: MuscleGroup::CHEST,
            description: 'test'
        );
        $this->repository->insert($exercise);
        $this->repository->fetchById(9999);
    }

    public function testSuccessfulInsertionOfMultipleExercises(): void
    {
        $exercise1 = new Exercise(
            name: 'Tests',
            muscleGroup: MuscleGroup::CHEST,
            description: 'test'
        );
        $insertedExercise1 = $this->repository->insert($exercise1);

        $exercise2 = new Exercise(
            name: 'Tests2',
            muscleGroup: MuscleGroup::CHEST,
            description: 'test'
        );
        $insertedExercise2 = $this->repository->insert($exercise2);

        $this->assertNotNull($insertedExercise1->getExerciseId());
        $this->assertNotNull($insertedExercise2->getExerciseId());

        $this->assertEquals($insertedExercise1->getName(), $exercise1->getName());
        $this->assertEquals($insertedExercise2->getName(), $exercise2->getName());
        $this->assertEquals($insertedExercise1->getMuscleGroup(), $exercise1->getMuscleGroup());
        $this->assertEquals($insertedExercise2->getMuscleGroup(), $exercise2->getMuscleGroup());
        $this->assertEquals($insertedExercise1->getDescription(), $exercise1->getDescription());
        $this->assertEquals($insertedExercise2->getDescription(), $exercise2->getDescription());

        $this->assertNotEquals($insertedExercise1->getExerciseId(), $insertedExercise2->getExerciseId());
    }

    public function testUpdateSuccessfullyUpdatesExercisesAttributes(): void
    {
        $exercise = new Exercise(
            name: 'Tests',
            muscleGroup: MuscleGroup::CHEST,
            description: 'test'
        );
        $insertedExercise = $this->repository->insert($exercise);
        $insertedExercise->setName('NewTests');
        $insertedExercise->setDescription('new-test');
        $insertedExercise->setMuscleGroup(MuscleGroup::ARMS);

        $this->assertNotNull($insertedExercise->getExerciseId());
        $this->assertEquals('NewTests', $insertedExercise->getName());
        $this->assertEquals('new-test', $insertedExercise->getDescription());
        $this->assertEquals(MuscleGroup::ARMS, $insertedExercise->getMuscleGroup());
    }

    public function testSuccessfulExerciseDeletion(): void
    {
        $this->expectException(ExerciseNotFoundException::class);

        $exercise = new Exercise(
            name: 'Tests',
            muscleGroup: MuscleGroup::CHEST,
            description: 'test'
        );

        $insertedExercise = $this->repository->insert($exercise);
        $exerciseId = $insertedExercise->getExerciseId();

        $this->repository->delete($exerciseId);
        $this->repository->fetchById($exerciseId);
    }
}
