<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Model;

enum MuscleGroup: string
{
    case CHEST = 'Chest';
    case BACK = 'Back';
    case LEGS = 'Legs';
    case ARMS = 'Arms';
    case SHOULDERS = 'Shoulders';
    case CORE = 'Core';
}
