<?php

declare(strict_types=1);

namespace Lexgur\Gondorgains\DependencyInjection;

use GondorGains\Exception\CircularDependencyException;
use GondorGains\Exception\MissingDependencyParameterException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $services;
    private array $parameters;
    private array $instantiating = [];

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
     * @throws CircularDependencyException
     * @throws MissingDependencyParameterException
     * @throws \ReflectionException
     */
    public function get(string $serviceClass): object
    {
        if (str_starts_with($serviceClass, 'GondorGains\Model')) {
            throw new \ReflectionException("Skipping Model classes: {$serviceClass}");
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
        } catch (\ReflectionException $e) {
            throw new \ReflectionException("Cannot instantiate {$serviceClass}: ".$e->getMessage());
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
