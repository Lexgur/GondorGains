<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests\Service;

use Lexgur\GondorGains\Service\ControllerParameterResolver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ControllerParameterResolverTest extends TestCase
{
    /**
     * @param array<string, mixed> $routeParams
     * @param array<int, mixed>    $expectedArgs
     */
    #[DataProvider('provideTestResolveParametersReturnsArrayData')]
    public function testResolveParametersReturnsAnArray(object $controller, array $routeParams, array $expectedArgs): void
    {
        $resolver = new ControllerParameterResolver();
        $result = $resolver->resolveParameters($controller, $routeParams);
        $this->assertSame($expectedArgs, $result);
    }

    /**
     * @param array<string, mixed> $routeParams
     * @throws \ReflectionException
     */
    #[DataProvider('provideResolveParametersExceptionData')]
    public function testResolveParametersThrowsException(object $controller, array $routeParams, string $expectedExceptionMessage): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $resolver = new ControllerParameterResolver();
        $resolver->resolveParameters($controller, $routeParams);
    }

    /**
     * @return array<string, array{0: object, 1: array<string, mixed>, 2: array<int, mixed>}>
     */
    public static function provideTestResolveParametersReturnsArrayData(): array
    {
        return [
            'required parameters matched' => [
                new class {
                    public function __invoke(string $string, int $integer): void {}
                },
                ['string' => 'hello', 'integer' => 42],
                ['hello', 42],
            ],
            'uses default for optional parameter' => [
                new class {
                    public function __invoke(string $string, int $integer = 10): void {}
                },
                ['string' => 'test'],
                ['test', 10],
            ],
            'extra route params ignored' => [
                new class {
                    public function __invoke(string $string): void {}
                },
                ['string' => 'val', 'unused' => 'ignored'],
                ['val'],
            ],
        ];
    }

    /**
     * @return array<string, array{0: object, 1: array<string, mixed>, 2: string}>
     */
    public static function provideResolveParametersExceptionData(): array
    {
        return [
            'missing required parameter' => [
                new class {
                    public function __invoke(string $string, int $integer): void {}
                },
                ['string' => 'hello'],
                'Missing required parameter: bar',
            ],
        ];
    }
}
