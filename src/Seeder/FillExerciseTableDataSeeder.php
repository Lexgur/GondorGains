<?php

namespace Lexgur\GondorGains\Seeder;

use Lexgur\GondorGains\Connection;
use Lexgur\GondorGains\Model\Exercise;
use Lexgur\GondorGains\Model\MuscleGroup;
use Lexgur\GondorGains\Repository\ExerciseModelRepository;
use Lexgur\GondorGains\Script\SeederInterface;

class FillExerciseTableDataSeeder implements SeederInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function order(): int
    {
        return 1;
    }

    public function seed(): void
    {
        echo static::class.PHP_EOL;
        $this->fillTable();
    }

    public function fillTable(): void
    {
        $exercises = [
            [
                'name' => 'Squats',
                'muscle_group' => MuscleGroup::LEGS,
                'description' => 'Brace yourself as if donning armor. With feet firm as a Gondorian shield wall, descend until your knees form a right angle—then rise with the might of Minas Tirith.',
            ],
            [
                'name' => 'Leg press',
                'muscle_group' => MuscleGroup::LEGS,
                'description' => 'Lie back as if resting before battle. Feet strong and wide, lower the weight like the gates of the White City, then drive upward with the power of Gondor’s cavalry.',
            ],
            [
                'name' => 'Back leg extensions',
                'muscle_group' => MuscleGroup::LEGS,
                'description' => 'Grip the machine like you would your sword hilt. Draw your legs back as though pulling a bowstring—release with control and grace, worthy of the Rangers.',
            ],
            [
                'name' => 'Calf raises, seated',
                'muscle_group' => MuscleGroup::LEGS,
                'description' => 'Raise your heels with the pride of a Númenórean king. Lower them till the very ground trembles beneath your feet like the drums of Mordor.',
            ],
            [
                'name' => 'Calf raises, standing',
                'muscle_group' => MuscleGroup::LEGS,
                'description' => 'Stand tall as the Tower of Ecthelion. As you ascend onto your toes, feel the strength of the White Tree coursing through your legs.',
            ],
            [
                'name' => 'Deadlift',
                'muscle_group' => MuscleGroup::BACK,
                'description' => 'Lift the weight as you would a fallen comrade in the heat of battle. Let your stance be strong and your will unbreakable.',
            ],
            [
                'name' => 'Dumbbell raise',
                'muscle_group' => MuscleGroup::SHOULDERS,
                'description' => 'Raise the dumbbells as if lifting the banners of your House. Eyes forward, back straight—let no shadow fall upon your form.',
            ],
            [
                'name' => 'Lateral raise',
                'muscle_group' => MuscleGroup::SHOULDERS,
                'description' => 'Spread your arms like the wings of Thorondor. Let your forearms soar to the sides, lifting Gondor’s might with each breath.',
            ],
            [
                'name' => 'Rear deltoids fly',
                'muscle_group' => MuscleGroup::SHOULDERS,
                'description' => 'Seated like a captain at the war council, open your arms wide—feeling the stretch of honor and strength across your back.',
            ],
            [
                'name' => 'Dumbbell push, laying on the back',
                'muscle_group' => MuscleGroup::CHEST,
                'description' => 'Lie beneath the weight as beneath the stars of Elendil. Push with resolve, returning the dumbbells to the heavens above.',
            ],
            [
                'name' => 'Push-ups',
                'muscle_group' => MuscleGroup::CHEST,
                'description' => 'With the discipline of the Tower Guard, lower yourself to the ground. Rise again as though defending the Citadel against shadow.',
            ],
            [
                'name' => 'Bench press',
                'muscle_group' => MuscleGroup::CHEST,
                'description' => 'Be steadfast as the White Tower. Lower the bar to your chest with the precision of an Elven arrow, then press it skyward with Gondorian pride.',
            ],
            [
                'name' => 'Dips',
                'muscle_group' => MuscleGroup::CHEST,
                'description' => 'Descend into the depths as if into the Paths of the Dead. Rise anew, unyielding as Isildur’s heir.',
            ],
            [
                'name' => 'Cable press-down',
                'muscle_group' => MuscleGroup::CHEST,
                'description' => 'Grip the handles like the reins of Shadowfax. Pull down until your hands cross like blades at council—swift and true.',
            ],
            [
                'name' => 'Machine chest-fly',
                'muscle_group' => MuscleGroup::CHEST,
                'description' => 'Set the bench with the precision of a siege weapon. Let your chest stretch as if drawing back the bow of Lórien—return with might.',
            ],
            [
                'name' => 'Lateral pull-down',
                'muscle_group' => MuscleGroup::BACK,
                'description' => 'Imagine the weight as the banner of Gondor. Pull it down with the strength of your bloodline, back straight, purpose clear.',
            ],
            [
                'name' => 'Back row',
                'muscle_group' => MuscleGroup::BACK,
                'description' => 'With legs anchored like fortress walls, draw the weight toward your heart—as though hauling a wounded ally from the battlefield.',
            ],
            [
                'name' => 'Pull-ups',
                'muscle_group' => MuscleGroup::BACK,
                'description' => 'Elevate yourself with the tenacity of Frodo on Mount Doom. Whether assisted or not, ascend with the will of the West.',
            ],
            [
                'name' => 'Seated bicep-curls',
                'muscle_group' => MuscleGroup::ARMS,
                'description' => 'Sit tall like a steward on the throne. With honor in each curl, lift the weight as if bearing Andúril anew.',
            ],
            [
                'name' => 'Standing dumbbell curl',
                'muscle_group' => MuscleGroup::ARMS,
                'description' => 'Stand firm, blades in hand. Curl each weight like drawing swords for the Last Alliance.',
            ],
            [
                'name' => 'Skull crushers',
                'muscle_group' => MuscleGroup::ARMS,
                'description' => 'Lie flat as the calm before a storm. Lower the weight behind your head with care—raise it again as if casting down a dark foe.',
            ],
            [
                'name' => 'Triceps pull-down',
                'muscle_group' => MuscleGroup::ARMS,
                'description' => 'Control the descent as if lowering the banner at dusk. Pull down with might, extend like a sword stroke in open battle.',
            ],
            [
                'name' => 'Crunches',
                'muscle_group' => MuscleGroup::CORE,
                'description' => 'Lie down as though beneath the stars of Ithilien. Rise just enough to glimpse the beacon lights—repeat until fire burns in your core.',
            ],
            [
                'name' => 'Leg raises, abs',
                'muscle_group' => MuscleGroup::CORE,
                'description' => 'Lie flat, arms braced like stone. Raise your legs like the sails of a ship bound for Valinor—lower them with grace, as if to dock at the Grey Havens.',
            ],
        ];

        $exerciseRepository = new ExerciseModelRepository($this->connection);

        foreach ($exercises as $exerciseData) {
            $exercise = Exercise::create($exerciseData);
            $exerciseRepository->save($exercise);
        }

        echo "Exercises have been inserted into the table." . PHP_EOL;
    }
}