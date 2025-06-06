<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Model\Exercise;
use Lexgur\GondorGains\Model\MuscleGroup;
use PHPUnit\Framework\TestCase;
use TypeError;

class ExerciseTest extends TestCase
{
    public function testConstructorSetsPropertiesWhenValidArgumentsProvided(): void
    {
        $exerciseId = 1;
        $exerciseName = "Test";
        $exerciseMuscleGroup = MuscleGroup::CHEST;
        $exerciseDescription = "Test";
        $exercise = new Exercise($exerciseName, $exerciseMuscleGroup, $exerciseDescription , $exerciseId);

        $this->assertEquals($exerciseId, $exercise->getExerciseId());
        $this->assertEquals($exerciseName, $exercise->getName());
        $this->assertEquals($exerciseMuscleGroup, $exercise->getMuscleGroup());
        $this->assertEquals($exerciseDescription, $exercise->getDescription());
    }

    public function testSetChallengeIdCorrectlySetsProperty(): void
    {
        $exercise = new Exercise(
            name: "Test Exercise",
            muscleGroup: MuscleGroup::CHEST,
            description: "Test description"
        );
        $this->assertNull($exercise->getChallengeId());

        $challengeId = 42;
        $exercise->setChallengeId($challengeId);

        $this->assertEquals($challengeId, $exercise->getChallengeId());
    }


    public function testConstructorThrowsTypeErrorWhenInvalidArgumentIsProvided(): void
    {

        $this->expectException(TypeError::class);

        $exerciseId = 'BadId';
        $exerciseName = "Test";
        $exerciseMuscleGroup = MuscleGroup::CHEST;
        $exerciseDescription = "Test";
        /** @phpstan-ignore-next-line argument.type */
        new Exercise($exerciseName, $exerciseMuscleGroup, $exerciseDescription , $exerciseId);
    }
}
