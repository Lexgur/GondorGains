<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Validation;

interface ValidatorInterface
{
    public function validate(mixed $input):bool;
}