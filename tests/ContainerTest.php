<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Exception\ServiceInstantiationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    #[DataProvider('provideTestContainerData')]
    final public function testContainer(string $serviceClass): void
    {
        // Initiate new container instance with parameters.
        $container = static::getContainer(withParameters: true);

        // Test if service is not yet initiated.
        $this->assertFalse($container->has($serviceClass));
        // Test if service is initiated successfully - instance exists.
        $this->assertInstanceOf($serviceClass, $container->get($serviceClass));
        // Test if service is already initiated.
        $this->assertTrue($container->has($serviceClass));
    }

    /** @return array<int, list<string>> */
    public static function provideTestContainerData(): array
    {
        return [
            [ServiceWithNoDependencies::class],
            [ServiceWithNoDependenciesAndNoConstruct::class],
            [ServiceWithSingleDependency::class],
            [ServiceWithMultipleDependencies::class],
            [ServiceWithMultipleDependantDependencies::class],
            [ServiceWithMultipleDependenciesExtendingAbstractService::class],
            [ServiceWithSingleParameterDependency::class],
            [ServiceWithMultipleParameterDependencies::class],
            [ServiceWithSingleDependencyAndParameterDependency::class],
            [ServiceWithMultipleDependenciesAndParameterDependencies::class],
        ];
    }

    #[DataProvider('provideTestContainerWithoutRequiredParametersData')]
    final public function testContainerWithoutRequiredParameters(string $serviceClass, bool $expectedException): void
    {
        // Initiate new container instance without parameters.
        $container = static::getContainer();
        // Test if service is not yet initialized.
        $this->assertFalse($container->has($serviceClass));

        if ($expectedException) {
            // Service should throw out MissingDependencyParameterException.
            $this->expectException(ServiceInstantiationException::class);
            $container->get($serviceClass);
        } else {
            // Test if service is initiated successfully - instance exists.
            $this->assertInstanceOf($serviceClass, $container->get($serviceClass));
            // Test if service is already initiated.
            $this->assertTrue($container->has($serviceClass));
        }
    }

    /** @return array<array{string, bool}> */
    public static function provideTestContainerWithoutRequiredParametersData(): array
    {
        return [
            [ServiceWithNoDependencies::class, false],
            [ServiceWithNoDependenciesAndNoConstruct::class, false],
            [ServiceWithSingleDependency::class, false],
            [ServiceWithMultipleDependencies::class, false],
            [ServiceWithMultipleDependantDependencies::class, false],
            [ServiceWithMultipleDependenciesExtendingAbstractService::class, false],
            [ServiceWithSingleParameterDependency::class, true],
            [ServiceWithMultipleParameterDependencies::class, true],
            [ServiceWithSingleDependencyAndParameterDependency::class, true],
            [ServiceWithMultipleDependenciesAndParameterDependencies::class, true],
        ];
    }

    #[DataProvider('provideTestCircularDependencyInServiceContainerData')]
    final public function testCircularDependencyInServiceContainer(string $serviceClass, bool $containerWithParameters): void
    {
        // Initiate new container instance with parameters.
        $container = static::getContainer(withParameters: $containerWithParameters);

        // Test if service is not yet initialized.
        $this->assertFalse($container->has(ServiceWithCircularDependencies::class));
        // Service should throw out CircularDependencyException.
        $this->expectException(ServiceInstantiationException::class);
        $container->get($serviceClass);
        $this->assertTrue($container->has($serviceClass));
    }

    /** @return array<array{string, bool}> */
    public static function provideTestCircularDependencyInServiceContainerData(): array
    {
        return [
            [ServiceWithCircularDependencies::class, true],
            [ServiceWithCircularDependantDependenciesAndMissingParameters::class, true],
            [ServiceWithCircularDependantDependenciesAndMissingParameters::class, false],
        ];
    }

    final public function testContainerThrowsReflectionClassExceptionWithNonExistentService(): void
    {
        $container = static::getContainer();

        $this->assertFalse($container->has('NonExistentService'));

        $this->expectException(ServiceInstantiationException::class);

        $container->get('NonExistentService');
    }

    public static function getContainer(bool $withParameters = false): Container
    {
        if (false === $withParameters) {
            return new Container();
        }

        return new Container([
            'stringParameter' => 'someString',
            'integerParameter' => 123,
            'booleanParameter' => true,
        ]);
    }
}

class ServiceWithNoDependencies
{
    public function __construct() {}
}

class ServiceWithNoDependenciesAndNoConstruct {}
class ServiceWithSingleDependency
{
    public function __construct(
        /** @phpstan-ignore property.onlyWritten */
        private ServiceWithNoDependencies $serviceWithNoDependencies
    ) {}
}

class ServiceWithMultipleDependencies
{
    public function __construct(
        /** @phpstan-ignore property.onlyWritten */
        private ServiceWithNoDependencies $serviceWithNoDependenciesFirst,
        /** @phpstan-ignore property.onlyWritten */
        private ServiceWithNoDependencies $serviceWithNoDependenciesSecond,
    ) {}
}

class ServiceWithMultipleDependantDependencies
{
    public function __construct(
        /** @phpstan-ignore property.onlyWritten */
        private ServiceWithNoDependencies $serviceWithNoDependenciesFirst,
        /** @phpstan-ignore property.onlyWritten */
        private ServiceWithSingleDependency $serviceWithSingleDependency,
        /** @phpstan-ignore property.onlyWritten */
        private ServiceWithMultipleDependencies $serviceWithMultipleDependencies,
    ) {}
}
abstract class AbstractServiceWithSingleDependency
{
    public function __construct(
        /** @phpstan-ignore property.onlyWritten */
        private ServiceWithNoDependencies $serviceWithNoDependencies
    ) {}
}

class ServiceWithMultipleDependenciesExtendingAbstractService extends AbstractServiceWithSingleDependency
{
    public function __construct(
        /** @phpstan-ignore property.onlyWritten */
        private ServiceWithNoDependencies $serviceWithNoDependencies,
        /** @phpstan-ignore property.onlyWritten */
        private ServiceWithSingleDependency $serviceWithSingleDependency,
        /** @phpstan-ignore property.onlyWritten */
        private ServiceWithMultipleDependencies $serviceWithMultipleDependencies,
        /** @phpstan-ignore property.onlyWritten */
        private ServiceWithMultipleDependantDependencies $serviceWithMultipleDependenciesSecond,
    ) {
        parent::__construct($serviceWithNoDependencies);
    }
}

class ServiceWithSingleParameterDependency
{
    public function __construct(
        /** @phpstan-ignore property.onlyWritten */
        private string $stringParameter,
    ) {}
}

class ServiceWithMultipleParameterDependencies
{
    public function __construct(
        /** @phpstan-ignore property.onlyWritten */
        private string $stringParameter,
        /** @phpstan-ignore property.onlyWritten */
        private int $integerParameter,
        /** @phpstan-ignore property.onlyWritten */
        private bool $booleanParameter,
    ) {}
}

class ServiceWithSingleDependencyAndParameterDependency
{
    public function __construct(
        /** @phpstan-ignore property.onlyWritten */
        private ServiceWithMultipleDependenciesExtendingAbstractService $serviceWithMultipleDependenciesExtendingAbstractService,
        /** @phpstan-ignore property.onlyWritten */
        private string $stringParameter,
    ) {}
}

class ServiceWithMultipleDependenciesAndParameterDependencies
{
    public function __construct(
        /** @phpstan-ignore property.onlyWritten */
        private ServiceWithMultipleDependenciesExtendingAbstractService $serviceWithMultipleDependenciesExtendingAbstractService,
        /** @phpstan-ignore property.onlyWritten */
        private ServiceWithMultipleParameterDependencies $serviceWithMultipleParameterDependencies,
        /** @phpstan-ignore property.onlyWritten */
        private string $stringParameter,
        /** @phpstan-ignore property.onlyWritten */
        private bool $booleanParameter,
        /** @phpstan-ignore property.onlyWritten */
        private int $integerParameter,
    ) {}
}

class ServiceWithCircularDependencies
{
    public function __construct(
        /** @phpstan-ignore property.onlyWritten */
        private ServiceWithCircularDependencies $serviceWithCircularDependencies,
    ) {}
}

class ServiceWithCircularDependantDependenciesAndMissingParameters
{
    public function __construct(
        /** @phpstan-ignore property.onlyWritten */
        private ServiceWithCircularDependencies $serviceWithCircularDependencies,
        /** @phpstan-ignore property.onlyWritten */
        private string $stringParameter,
    ) {}
}
