<?php

declare(strict_types=1);

namespace Lexgur\GondorGains;

use ReflectionException;

class ClassFinder
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /** @return list<string> */
    public function findClassesImplementing(string $interface): array
    {
        return $this->processPhpFiles(function (\ReflectionClass $reflectionClass) use ($interface) : bool {
            return !$reflectionClass->isInterface() && $reflectionClass->implementsInterface($interface);
        });
    }

    /** @return list<string> */
    public function findClassesExtending(string $abstractClass): array
    {
        return $this->processPhpFiles(function (\ReflectionClass $reflectionClass) use ($abstractClass) : bool {
            return $reflectionClass->isSubclassOf($abstractClass);
        });
    }

    /** @return list<string> */
    public function findClassesInNamespace(string $namespace): array
    {
        return $this->processPhpFiles(function (\ReflectionClass $reflectionClass) use ($namespace) : bool {
            $classNamespace = $reflectionClass->getNamespaceName();
            return str_starts_with($classNamespace, $namespace);
        });
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
            return null;
        }

        $namespace = null;
        if (preg_match('/namespace\s+(.+);/', $content, $namespaceMatch)) {
            $namespace = trim($namespaceMatch[1]);
        }
        if (preg_match('/(?:class|interface|trait|enum)\s+([^\s{]+)(\s+(extends|implements)\s+[^\s{]+)?/', $content, $classMatch)) {
            $className = trim($classMatch[1]);

            return $namespace ? $namespace . '\\' . $className : $className;
        }

        return null;
    }

    /** @return list<string>
     * @throws ReflectionException
     */
    private function processPhpFiles(callable $condition): array
    {
        $phpFiles = $this->getPhpFiles();
        $results = [];

        foreach ($phpFiles as $file) {
                $path = $file->getPathname();
                $className = $this->getFullClassName($path);
                $reflectionClass = new \ReflectionClass($className);

                if ($condition($reflectionClass, $className)) {
                    $results[] = $className;
                }
        }
        return $results;
    }
}