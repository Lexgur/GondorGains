<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Service;

class ControllerParameterResolver
{
    /**
     * Resolves parameters to be passed into a controller's __invoke method.
     *
     * @param object               $controller  the controller instance
     * @param array<string, mixed> $routeParams parameters extracted from the route
     *
     * @return array<int, mixed> list of arguments to pass to the controller's __invoke method
     *
     * @throws \ReflectionException
     */
    public function resolveParameters(object $controller, array $routeParams): array
    {
        $reflection = new \ReflectionMethod($controller, '__invoke');
        $invokeParams = $reflection->getParameters();

        $args = [];

        foreach ($invokeParams as $param) {
            $name = $param->getName();
            if (array_key_exists($name, $routeParams)) {
                $args[] = $routeParams[$name];
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } else {
                throw new \InvalidArgumentException("Missing required parameter: {$name}");
            }
        }

        return $args;
    }
}
