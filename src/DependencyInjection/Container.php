<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\DependencyInjection;

use Lexgur\GondorGains\Exception\CircularDependencyException;
use Lexgur\GondorGains\Exception\MissingDependencyParameterException;
use Lexgur\GondorGains\Exception\ServiceInstantiationException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * @var array<string, object> Stores instantiated service objects.
     */
    private array $services;
    /**
     * @var array<string, string|int|bool|float> Stores configuration parameters.
     */
    private array $parameters;
    /**
     * @var array<string, bool> Tracks services currently being instantiated to prevent circular dependencies.
     */
    private array $instantiating = [];

    /**
     * @param array<string, string|int|bool|float> $parameters Configuration parameters like DB credentials or paths.
     * @param array<string, object> $services Pre-instantiated services, mapped by class name.
     */
    public function __construct(array $parameters = [], array $services = [])
    {
        $this->parameters = $parameters;
        $this->services = $services;
    }

    public function has(string $serviceClass): bool
    {
        return isset($this->services[$serviceClass]);
    }

    public function hasParameter(string $name): bool
    {
        return isset($this->parameters[$name]);
    }

    /**
     * @throws MissingDependencyParameterException
     */
    public function getParameter(string $name): mixed
    {
        if (!$this->hasParameter($name)) {
            throw new MissingDependencyParameterException("Missing parameter: {$name}");
        }

        return $this->parameters[$name];
    }

    public function bind(string $serviceClass, object $service): void
    {
        $this->services[$serviceClass] = $service;
    }

    /**
     * @throws CircularDependencyException|ServiceInstantiationException
     */
    public function get(string $serviceClass): object
    {
        if (str_starts_with($serviceClass, 'Lexgur\GondorGains\Model')) {
            throw new ServiceInstantiationException("Skipping Model classes: {$serviceClass}");
        }

        if ($this->has($serviceClass)) {
            return $this->services[$serviceClass];
        }

        if (isset($this->instantiating[$serviceClass])) {
            throw new CircularDependencyException("Circular dependency detected for: {$serviceClass}");
        }

        $this->instantiating[$serviceClass] = true;

        try {
            $reflectionClass = new \ReflectionClass($serviceClass);

            $dependencies = [];
            $constructor = $reflectionClass->getConstructor();
            $arguments = $constructor?->getParameters() ?? [];
            foreach ($arguments as $argument) {
                if ($argument->getType()->isBuiltin()) {
                    $dependencies[] = $this->resolveParameter($argument->getName());
                } else {
                    $dependencies[] = $this->get($argument->getType()->getName());
                }
            }

            $instance = $reflectionClass->newInstanceArgs($dependencies);

            $this->services[$serviceClass] = $instance;
            unset($this->instantiating[$serviceClass]);

            return $instance;
        } catch (\Throwable $e) {
            throw new ServiceInstantiationException("Cannot instantiate {$serviceClass}: ".$e->getMessage());
        }
    }

    /**
     * Resolves a scalar parameter.
     *
     * @throws MissingDependencyParameterException
     */
    private function resolveParameter(string $parameterName): mixed
    {
        if (!isset($this->parameters[$parameterName])) {
            throw new MissingDependencyParameterException("Cannot resolve parameter: {$parameterName}");
        }

        return $this->parameters[$parameterName];
    }
}
