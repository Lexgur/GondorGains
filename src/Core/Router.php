<?php

namespace Lexgur\GondorGains\Core;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Exception\IncorrectRoutePathException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

class Router
{
    private const CONTROLLER_DIR = __DIR__.'/../Controller';

    /**
     * @var array<string, string>
     */
    private array $routes = [];

    /**
     * @throws IncorrectRoutePathException
     */
    public function registerControllers(): void
    {
        $phpFiles = $this->getPhpFiles();

        foreach ($phpFiles as $file) {
            try {
                $filePath = $file->getPathname();
                $className = $this->getFullClassName($filePath);
                $reflectionClass = new \ReflectionClass($className);
                $classAttributes = $reflectionClass->getAttributes(Path::class);
                $routePath = $classAttributes[0]?->newInstance()->getPath();
                if ('' !== $routePath) {
                    $this->routes[$routePath] = $className;
                }
            } catch (\Throwable $e) {
                throw new \RuntimeException('An error occurred while registering controllers: '.$e->getMessage());
            }
        }
    }

    /**
     * @return RegexIterator<RecursiveIteratorIterator, string>
     */
    public function getPhpFiles(): RegexIterator
    {
        $directoryIterator = new RecursiveDirectoryIterator(self::CONTROLLER_DIR);
        $iterator = new RecursiveIteratorIterator($directoryIterator);

        return new RegexIterator($iterator, '/\.php$/');
    }

    public function getFullClassName(string $filePath): ?string
    {
        $content = file_get_contents($filePath);
        if ('' === $content) {
            throw new \RuntimeException("Failed to read file: {$filePath}");
        }

        $namespace = null;
        if (preg_match('/namespace\s+(.+);/', $content, $namespaceMatch)) {
            $namespace = trim($namespaceMatch[1]);
        }
        if (preg_match('/class\s+([^\s{]+)/', $content, $classMatch)) {
            $className = trim($classMatch[1]);

            return $namespace ? $namespace.'\\'.$className : $className;
        }

        throw new IncorrectRoutePathException('Class not found: '.$filePath);
    }

    public function getController(string $routePath): string
    {
        foreach ($this->routes as $routePattern => $controllerClass) {
            $regexPattern = preg_replace('/:(\w+)/', '(?P<$1>\d+)', $routePattern);
            $regexPattern = '#^'.$regexPattern.'$#';

            if (preg_match($regexPattern, $routePath)) {
                return $controllerClass;
            }
        }

        throw new IncorrectRoutePathException("Route path '{$routePath}' not found.");
    }

    /**
     * @return string[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
