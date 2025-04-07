<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\ClassFinder;
use Lexgur\GondorGains\Tests\ClassFinderTest\FindClassesExtending\AbstractExample;
use Lexgur\GondorGains\Tests\ClassFinderTest\FindClassesExtending\AbstractInterface;
use Lexgur\GondorGains\Tests\ClassFinderTest\FindClassesExtending\AbstractNotExtendedExample;
use Lexgur\GondorGains\Tests\ClassFinderTest\FindClassesExtending\AbstractNotExtendedInterface;
use Lexgur\GondorGains\Tests\ClassFinderTest\FindClassesExtending\FirstInterface;
use Lexgur\GondorGains\Tests\ClassFinderTest\FindClassesImplementing\AndAnotherClassInterface;
use Lexgur\GondorGains\Tests\ClassFinderTest\FindClassesImplementing\AnotherExampleInterface;
use Lexgur\GondorGains\Tests\ClassFinderTest\FindClassesImplementing\ExampleInterface;
use Lexgur\GondorGains\Tests\ClassFinderTest\FindClassesImplementing\FirstClass;
use Lexgur\GondorGains\Tests\ClassFinderTest\FindClassesImplementing\NotUsedClassInterface;
use Lexgur\GondorGains\Tests\ClassFinderTest\FindClassesImplementing\SecondClass;
use Lexgur\GondorGains\Tests\ClassFinderTest\FindClassesImplementing\ThirdClass;
use Lexgur\GondorGains\Tests\ClassFinderTest\FindClassesInNamespace\FirstNameSpace\FirstEnum;
use Lexgur\GondorGains\Tests\ClassFinderTest\FindClassesInNamespace\FirstNameSpace\FirstTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ClassFinderTest extends TestCase
{
    private ClassFinder $classFinder;
    protected function setUp(): void
    {
        $this->classFinder = new ClassFinder(__DIR__);
    }

    #[DataProvider('provideTestFindClassesImplementingData')]
    public function testFindClassesImplementing(string $value, array $expected): void
    {
        $actual = $this->classFinder->findClassesImplementing();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array<int, list<list<string>|string>>
     */
    public static function provideTestFindClassesImplementingData(): array
    {
        return [
            [
                ExampleInterface::class,
                [
                    FirstClass::class,
                ],
            ],
            [
                AnotherExampleInterface::class,
                [
                    SecondClass::class,
                    ThirdClass::class,
                ],
            ],
            [
                AndAnotherClassInterface::class,
                [
                    ThirdClass::class,
                ],
            ],
            [
                NotUsedClassInterface::class,
                [],
            ],
        ];
    }

    #[DataProvider('provideTestFindClassesExtendingData')]
    public function testFindClassesExtending(string $value, array $expected): void
    {
        $actual = $this->classFinder->findClassesExtending($value);
        $this->assertEquals($expected, $actual);
    }

    public static function provideTestFindClassesExtendingData(): array
    {
        return [
            [
                AbstractExample::class,
                [
                    ClassFinderTest\FindClassesExtending\FirstClass::class,
                ]
            ],
            [
                AbstractNotExtendedExample::class,
                [],
            ],
            [
                AbstractInterface::class,
                [
                    FirstInterface::class,
                ]
            ],
            [
                AbstractNotExtendedInterface::class,
                [],
            ]
        ];
    }

    #[DataProvider('provideTestFindClassesInNamespaceData')]
    public function testFindClassesInNamespace(string $value, array $expected): void
    {
        $actual = $this->classFinder->findClassesInNamespace($value);
        $this->assertEquals($expected, $actual);
    }

    public static function provideTestFindClassesInNamespaceData(): array
    {
        return [
            [
                "Lexgur\GondorGains\Tests\ClassFinderTest\FindClassesInNamespace",
                [
                    FirstEnum::class,
                    ClassFinderTest\FindClassesInNamespace\FirstNameSpace\FirstInterface::class,
                    FirstTrait::class,
                    ClassFinderTest\FindClassesInNamespace\FirstNameSpace\FirstClass::class,
                    ClassFinderTest\FindClassesInNamespace\FirstNameSpace\SecondNamespace\FirstClass::class,
                    ClassFinderTest\FindClassesInNamespace\SecondNamespace\FirstClass::class,
                ],
            ],
            [
                "Lexgur\GondorGains\Tests\ClassFinderTest\FindClassesInNamespace\FirstNameSpace",
                [
                    FirstEnum::class,
                    ClassFinderTest\FindClassesInNamespace\FirstNameSpace\FirstInterface::class,
                    FirstTrait::class,
                    ClassFinderTest\FindClassesInNamespace\FirstNameSpace\FirstClass::class,
                    ClassFinderTest\FindClassesInNamespace\FirstNameSpace\SecondNamespace\FirstClass::class,
                ],
            ],
            [
                "Lexgur\GondorGains\Tests\ClassFinderTest\FindClassesInNamespace\FirstNameSpace\SecondNamespace",
                [
                    ClassFinderTest\FindClassesInNamespace\FirstNameSpace\SecondNamespace\FirstClass::class,
                ],
            ],
            [
                "Lexgur\GondorGains\Tests\ClassFinderTest\FindClassesInNamespace\SecondNamespace",
                [
                    ClassFinderTest\FindClassesInNamespace\SecondNamespace\FirstClass::class,
                ],
            ],
            [
                "Lexgur\GondorGains\Tests\ClassFinderTest\FindClassesInNamespace\ThirdNamespace",
                [],
            ],
        ];
    }

}