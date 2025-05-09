<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests\Service;

use Lexgur\GondorGains\Exception\CircularDependencyException;
use Lexgur\GondorGains\Service\SeederDependencyResolver;
use PHPUnit\Framework\TestCase;

class SeederDependencyResolverTest extends TestCase
{
    private SeederDependencyResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new SeederDependencyResolver();
    }

    public function testSortsSeedersWithDependencies(): void
    {
        $seeders = [SeederC::class, SeederA::class, SeederB::class, SeederD::class];
        $result = $this->resolver->sortSeeders($seeders);

        $this->assertEquals([SeederA::class, SeederB::class, SeederC::class, SeederD::class], $result);
    }

    public function testOnCircularDependenciesThrowsCircularDependencyException(): void
    {
        $this->expectException(CircularDependencyException::class);

        $seeders = [CircularSeeder1::class, CircularSeeder2::class];
        $this->resolver->sortSeeders($seeders);
    }
}
class SeederA
{
    /** @return array<string> */
    public static function dependencies(): array
    {
        return [];
    }
}
class SeederB
{
    /** @return array<string> */
    public static function dependencies(): array
    {
        return [SeederA::class];
    }
}
class SeederC
{
    /** @return array<string> */
    public static function dependencies(): array
    {
        return [SeederB::class, SeederA::class];
    }
}
class SeederD
{
    /** @return array<string> */
    public static function dependencies(): array
    {
        return [SeederB::class, SeederA::class, SeederC::class];
    }
}

class CircularSeeder1
{
    /** @return array<string> */
    public static function dependencies(): array
    {
        return [CircularSeeder2::class];
    }
}
class CircularSeeder2
{
    /** @return array<string> */
    public static function dependencies(): array
    {
        return [CircularSeeder1::class];
    }
}
