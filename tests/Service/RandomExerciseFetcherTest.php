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
    public function testShouldReturnCorrectNumberOfExercisesForRotation(int $rotation, int $expected): void
    {
        $this->seedExercisesFixed(2);

        $result = $this->exerciseFetcher->fetchRandomExercise($rotation);

        $this->assertCount($expected, $result);
    }

    /** @return array<string, list<int>> */
    public static function provideTestShouldReturnValidNumberOfExercisesForRotation(): array
    {
        return [
            'rotation_1' => [1, 8, 8],
            'rotation_2' => [2, 4, 4],
        ];
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
        $this->seedExercisesFixed(3);

        $seen = [];
        for ($i = 0; $i < 3; ++$i) {
            $rotation = $this->exerciseFetcher->getNextRotation();
            $seen[] = $rotation;
            $this->exerciseFetcher->fetchRandomExercise($rotation);
        }

        $this->assertCount(3, array_unique($seen));
    }

    /**
     * @throws RandomException
     */
    public function testShouldResetExercisesWhenAllUsed(): void
    {
        $this->seedExercisesFixed(2);

        $first = $this->exerciseFetcher->fetchRandomExercise(2);
        $second = $this->exerciseFetcher->fetchRandomExercise(2);

        $this->assertCount(4, $first);
        $this->assertCount(4, $second);
    }

    public function testGetNextRotationTriggersLazyInitialization(): void
    {
        $reflection = new \ReflectionClass($this->exerciseFetcher);
        $property = $reflection->getProperty('muscleGroupRotationSequence');
        $property->setValue($this->exerciseFetcher, []);
        $rotation = $this->exerciseFetcher->getNextRotation();

        $this->assertContains($rotation, [0, 1, 2]);
    }

    private function seedExercisesFixed(int $countPerGroup): void
    {
        foreach (MuscleGroup::cases() as $group) {
            for ($i = 0; $i < $countPerGroup; ++$i) {
                $exercise = new Exercise("Test {$group->value} {$i}", $group, "desc {$i}");
                $this->repository->insert($exercise);
            }
        }
    }
}
