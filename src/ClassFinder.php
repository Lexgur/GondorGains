<?php

declare(strict_types=1);

namespace Lexgur\GondorGains;

use Lexgur\GondorGains\Exception\FilePathReadException;
use Lexgur\GondorGains\Exception\FindClassesInNamespaceException;
use Lexgur\GondorGains\Exception\FindingClassesImplementingInterfaceException;
use Lexgur\GondorGains\Exception\IncorrectRoutePathException;

class ClassFinder
{
    private const DIR = __DIR__;

    /**
     * @var array<string, string>
     */
    private array $implementingClasses = [];

    /**
     * @var array<string, string>
     */
    private array $extendingClasses = [];

    /**
     * @var array<string, string>
     */
    private array $namespaceClasses = [];

    private string $path;

    public function __construct($path = self::DIR)
    {
        $this->path = $path;
    }

    /**
     * @throws FindingClassesImplementingInterfaceException
     */
    public function findClassesImplementing(string $interface): array
    {
        $phpFiles = $this->getPhpFiles();
        $this->implementingClasses = [];

        foreach ($phpFiles as $file) {
            try {
                $fileInfo = $file;
                $path = $fileInfo->getPathname();

                $className = $this->getFullClassName($path);

                if (!$className || !class_exists($className)) {
                    continue;
                }

                $reflectionClass = new \ReflectionClass($className);

                if ($reflectionClass->implementsInterface($interface)) {
                    $this->implementingClasses[] = $className;
                }
            } catch (\Throwable $e) {
                throw new FindingClassesImplementingInterfaceException(
                    'Error while scanning file "' . $file . '": ' . $e->getMessage()
                );
            }
        }

        return $this->implementingClasses;
    }

    public function findClassesExtending(string $abstractClass) : array
    {
        $phpFiles = $this->getPhpFiles();
        $this->extendingClasses = [];

        foreach ($phpFiles as $file) {
            try {
                $fileInfo = $file;
                $path = $fileInfo->getPathname();

                $className = $this->getFullClassName($path);


                if (!$className || !class_exists($className) && !interface_exists($className)) {
                    continue;
                }

                $reflectionClass = new \ReflectionClass($className);

                if ($reflectionClass->isSubclassOf($abstractClass)) {
                    $this->extendingClasses[] = $className;
                } elseif ($reflectionClass->isInterface()) {
                    if (is_subclass_of($className, $abstractClass)) {
                        $this->extendingClasses[] = $className;
                    }
                }
            } catch (\Throwable $e) {
                throw new FindingClassesImplementingInterfaceException(
                    'Error while scanning file "' . $file . '": ' . $e->getMessage()
                );
            }
        }

        return $this->extendingClasses;
    }

    public function findClassesInNamespace(string $namespace) : array
    {
        $phpFiles = $this->getPhpFiles();
        $this->namespaceClasses = [];

        foreach ($phpFiles as $file) {
            try {
                $fileInfo = $file;
                $path = $fileInfo->getPathname();

                $className = $this->getFullClassName($path);
                if (!$className){
                    continue;
                }
                $reflectionClass = new \ReflectionClass($className);
                $classNamespace = $reflectionClass->getNamespaceName();

                if ($classNamespace === $namespace || str_contains($classNamespace, $namespace . '\\')) {
                    $this->namespaceClasses[] = $className;
                }
            } catch (\Throwable $e) {
                throw new FindClassesInNamespaceException(
                    'Error while scanning file "' . $file . '": ' . $e->getMessage()
                );
            }
        }
        sort($this->namespaceClasses);

        return $this->namespaceClasses;
    }

    /**
     * @return \RegexIterator<int, \SplFileInfo, \RecursiveIteratorIterator<\RecursiveDirectoryIterator>>
     */
    public function getPhpFiles(): \RegexIterator
    {
        $directoryIterator = new \RecursiveDirectoryIterator($this->path, \FilesystemIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directoryIterator);

        /** @var \RegexIterator<int, \SplFileInfo, \RecursiveIteratorIterator<\RecursiveDirectoryIterator>> $regexIterator */
        $regexIterator = new \RegexIterator($iterator, '/\.php$/i', \RegexIterator::MATCH);
        return $regexIterator;
    }

    public function getFullClassName(string $path): ?string
    {
        $content = file_get_contents($path);
        if (empty($content)) {
            throw new FilePathReadException("Failed to read file: {$path}");
        }

        $namespace = null;
        if (preg_match('/namespace\s+(.+);/', $content, $namespaceMatch)) {
            $namespace = trim($namespaceMatch[1]);
        }
        if (preg_match('/(?:class|interface|trait|enum)\s+([^\s{]+)(\s+(extends|implements)\s+[^\s{]+)?/', $content, $classMatch)) {
            $className = trim($classMatch[1]);

            return $namespace ? $namespace . '\\' . $className : $className;
        }

        throw new IncorrectRoutePathException('Class not found: '.$path);
    }
}