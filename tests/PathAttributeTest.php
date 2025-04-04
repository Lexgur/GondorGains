<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Attribute\Path;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class PathAttributeTest extends TestCase
{
    public function testGetPath(): void
    {
        $class = new ReflectionClass(SomethingCreateController::class);
        $attributes = $class->getAttributes(Path::class);
        $this->assertEquals('/something/create', $attributes[0]->newInstance()->getPath());
    }
}

#[Path('/something/create')]
class SomethingCreateController {}
