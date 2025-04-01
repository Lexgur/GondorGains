<?php

namespace Lexgur\GondorGains;

use FilesystemIterator;
use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Exception\IncorrectRoutePathException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use RegexIterator;
use RuntimeException;
use SplFileInfo;
use Throwable;

class Router
{
    private const CONTROLLER_DIR = __DIR__ . '/Controller';

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
                $file = new SplFileInfo($file);
                $filePath = $file->getPathname();
                $className = $this->getFullClassName($filePath);
                $reflectionClass = new ReflectionClass($className);
                $classAttributes = $reflectionClass->getAttributes(Path::class);
                $routePath = $classAttributes[0]?->newInstance()->getPath();
                if ('' !== $routePath) {
                    $this->routes[$routePath] = $className;
                }
            } catch (Throwable $e) {
                throw new RuntimeException('An error occurred while registering controllers: ' . $e->getMessage());
            }
        }
    }

    /**
     * @return RegexIterator<int, SplFileInfo, RecursiveIteratorIterator<RecursiveDirectoryIterator>>
     */
    public function getPhpFiles(): RegexIterator
    {
        $directoryIterator = new RecursiveDirectoryIterator(self::CONTROLLER_DIR, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directoryIterator);

        /** @var RegexIterator<int, SplFileInfo, RecursiveIteratorIterator<RecursiveDirectoryIterator>> $regexIterator */
        $regexIterator = new RegexIterator($iterator, '/\.php$/i', RegexIterator::MATCH);

        return $regexIterator;
    }

    public function getFullClassName(string $filePath): ?string
    {
        $content = file_get_contents($filePath);
        if ('' === $content) {
            throw new RuntimeException("Failed to read file: {$filePath}");
        }

        $namespace = null;
        if (preg_match('/namespace\s+(.+);/', $content, $namespaceMatch)) {
            $namespace = trim($namespaceMatch[1]);
        }
        if (preg_match('/class\s+([^\s{]+)/', $content, $classMatch)) {
            $className = trim($classMatch[1]);

            return $namespace ? $namespace . '\\' . $className : $className;
        }

        throw new IncorrectRoutePathException('Class not found: ' . $filePath);
    }

    public function getController(string $routePath): string
    {
        foreach ($this->routes as $routePattern => $controllerClass) {
            $regexPattern = preg_replace('/:(\w+)/', '(?P<$1>\d+)', $routePattern);
            $regexPattern = '#^' . $regexPattern . '$#';

            if (preg_match($regexPattern, $routePath)) {
                return $controllerClass;
            }
        }

        throw new IncorrectRoutePathException("404, Not Found: The route '{$routePath}' does not exist.");
    }

    /**
     * @return string[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
