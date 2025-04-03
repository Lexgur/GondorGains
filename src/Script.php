<?php

declare(strict_types=1);

namespace Lexgur\GondorGains;

use Lexgur\GondorGains\Exception\IncorrectScriptNameException;
use Lexgur\GondorGains\Exception\ScriptFailedToRunException;
use Lexgur\GondorGains\Script\ScriptInterface;
use Lexgur\GondorGains\Validation\ScriptNameValidator;

class Script
{
    protected Container $container;

    public function __construct()
    {
        $this->container = new Container();
    }

    public function run(string $scriptClass): int
    {
        $className = $this->getClassName($scriptClass);
        $service = $this->container->get($className);

        if (!$service instanceof ScriptInterface) {
            throw new ScriptFailedToRunException('Script not found');
        }

        return $service->run();
    }

    private function getClassName(string $scriptClass): string
    {
        if (str_contains($scriptClass, '//')) {
            throw new IncorrectScriptNameException("Invalid script class name: Consecutive forward slashes are not allowed.");
        }

        $scriptClass = preg_replace('/\\\\{3,}/', '\\\\', $scriptClass);

        $scriptClass = str_replace('/', '\\', $scriptClass);

        $scriptClass = preg_replace('/\\\\{2}/', '\\', $scriptClass);

        ScriptNameValidator::validate($scriptClass);

        return $scriptClass;
    }
}
