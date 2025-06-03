<?php

namespace Lexgur\GondorGains\Tests\Service;

use Lexgur\GondorGains\Connection;
use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Exception\CircularDependencyException;
use Lexgur\GondorGains\Exception\NotEnoughExercisesException;
use Lexgur\GondorGains\Model\Exercise;
use Lexgur\GondorGains\Model\MuscleGroup;
use Lexgur\GondorGains\Repository\ExerciseModelRepository;
use Lexgur\GondorGains\Script\RunMigrationsScript;
use Lexgur\GondorGains\Service\RandomExerciseFetcher;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Random\RandomException;

class RandomExerciseFetcherTest extends TestCase
{
    private ExerciseModelRepository $repository;
    private RandomExerciseFetcher $exerciseFetcher;

    /**
     * @throws CircularDependencyException
     */
    protected function setUp(): void
    {
        $config = require __DIR__.'/../../config.php';
        $container = new Container($config);

        $runMigrations = $container->get(RunMigrationsScript::class);
        $runMigrations->run();

        $connection = $container->get(Connection::class);
        $connection->connect()->exec('DELETE FROM exercises');

        $this->repository = $container->get(ExerciseModelRepository::class);
        $this->exerciseFetcher = new RandomExerciseFetcher($this->repository);
    }

    /**
     * @throws RandomException
     */
    #[DataProvider('provideTestShouldReturnValidNumberOfExercisesForRotation')]
    public function testShouldReturnValidNumberOfExercisesForRotation(int $rotation, int $min, int $max): void
    {
        $this->seedExercises();

        $result = $this->exerciseFetcher->fetchRandomExercise($rotation);

        $this->assertGreaterThanOrEqual($min, count($result));
        $this->assertLessThanOrEqual($max, count($result));
    }

    /**
     * @throws RandomException
     */
    public function testShouldThrowExceptionForInvalidRotation(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->exerciseFetcher->fetchRandomExercise(99);
    }

    /**
     * @throws RandomException
     */
    public function testShouldThrowNotEnoughExercisesException(): void
    {
        $this->expectException(NotEnoughExercisesException::class);

        $exercise = new Exercise('Too Little', MuscleGroup::CHEST, 'desc');
        $this->repository->insert($exercise);
        $this->exerciseFetcher->fetchRandomExercise(1);
    }

    /**
     * @throws RandomException
     */
    public function testShouldProvideNonRepeatingRotationSequence(): void
    {
        $this->seedExercises();

        $seen = [];
        for ($i = 0; $i < 3; ++$i) {
            $rotation = $this->exerciseFetcher->getNextRotation();
            $seen[] = $rotation;
            $this->exerciseFetcher->fetchRandomExercise($rotation);
        }

        $this->assertCount(3, array_unique($seen));
        $this->assertEqualsCanonicalizing([1, 2, 3], $seen);
    }

    /**
     * @throws RandomException
     */
    public function testShouldResetExercisesWhenAllUsed(): void
    {
        $this->seedExercises(minPerGroup: 2, maxPerGroup: 2);

        $first = $this->exerciseFetcher->fetchRandomExercise(2);
        $second = $this->exerciseFetcher->fetchRandomExercise(2);

        $this->assertCount(8, $first);
        $this->assertCount(8, $second);
    }

    /** @return array<string, array{int, int, int}> */
    public static function provideTestShouldReturnValidNumberOfExercisesForRotation(): array
    {
        return [
            'rotation_1' => [1, 4, 6],
            'rotation_2' => [2, 8, 12],
        ];
    }

    public function testGetNextRotationTriggersLazyInitialization(): void
    {
        $reflection = new \ReflectionClass($this->exerciseFetcher);
        $property = $reflection->getProperty('muscleGroupRotationSequence');
        $property->setValue($this->exerciseFetcher, []);
        $rotation = $this->exerciseFetcher->getNextRotation();

        $this->assertContains($rotation, [1, 2, 3]);
    }

    /**
     * Utility to seed exercises into the test database.
     */
    private function seedExercises(int $minPerGroup = 3, int $maxPerGroup = 4): void
    {
        foreach (MuscleGroup::cases() as $group) {
            $count = rand($minPerGroup, $maxPerGroup);
            for ($i = 0; $i < $count; ++$i) {
                $exercise = new Exercise("Test $group->value $i", $group, "desc $i");
                $this->repository->insert($exercise);
            }
        }
    }
}
