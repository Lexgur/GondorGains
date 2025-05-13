<?php

namespace Lexgur\GondorGains;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Exception\FilePathReadException;
use Lexgur\GondorGains\Exception\NotFoundException;
use Lexgur\GondorGains\Exception\RegisterControllerException;

class Router
{
    /**
     * @var array<string, string>
     */
    private array $routes = [];

    private string $controllerDir;

    public function __construct(string $controllerDir)
    {
        $this->controllerDir = $controllerDir;
    }

    /**
     * @throws NotFoundException
     */
    public function registerControllers(): void
    {
        $phpFiles = $this->getPhpFiles();

        foreach ($phpFiles as $file) {
            try {
                $file = new \SplFileInfo($file);
                $filePath = $file->getPathname();
                $className = $this->getFullClassName($filePath);
                $reflectionClass = new \ReflectionClass($className);
                $classAttributes = $reflectionClass->getAttributes(Path::class);
                $routePath = $classAttributes[0]->newInstance()->getPath();
                if (!empty($routePath)) {
                    $this->routes[$routePath] = $className;
                }
            } catch (\Throwable $e) {
                throw new RegisterControllerException('An error occurred while registering controllers: ' . $e->getMessage());
            }
        }
    }

    /**
     * @return \RegexIterator<int, \SplFileInfo, \RecursiveIteratorIterator<\RecursiveDirectoryIterator>>
     */
    public function getPhpFiles(): \RegexIterator
    {
        $directoryIterator = new \RecursiveDirectoryIterator($this->controllerDir, \FilesystemIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directoryIterator);

        /** @var \RegexIterator<int, \SplFileInfo, \RecursiveIteratorIterator<\RecursiveDirectoryIterator>> $regexIterator */
        $regexIterator = new \RegexIterator($iterator, '/\.php$/i', \RegexIterator::MATCH);
        return $regexIterator;
    }

    public function getFullClassName(string $filePath): ?string
    {
        $content = file_get_contents($filePath);
        if (empty($content)) {
            throw new FilePathReadException("Failed to read file: {$filePath}");
        }

        $namespace = null;
        if (preg_match('/namespace\s+(.+);/', $content, $namespaceMatch)) {
            $namespace = trim($namespaceMatch[1]);
        }
        if (preg_match('/class\s+([^\s{]+)/', $content, $classMatch)) {
            $className = trim($classMatch[1]);

            return $namespace ? $namespace . '\\' . $className : $className;
        }

        throw new NotFoundException('Class not found: ' . $filePath);
    }

    public function getController(string $routePath): array
    {
        foreach ($this->routes as $routePattern => $controllerClass) {
            $regexPattern = preg_replace('/:(\w+)/', '(?P<$1>\d+)', $routePattern);
            $regexPattern = '#^' . $regexPattern . '$#';

            if (preg_match($regexPattern, $routePath, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                foreach ($params as $key => $value) {
                    if (is_numeric($value)) {
                        $params[$key] = (int)$value;
                    }
                }
                return [$controllerClass, $params];
            }
        }

        throw new NotFoundException("404, Not Found: The route '{$routePath}' does not exist.");
    }

    /**
     * @return string[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
