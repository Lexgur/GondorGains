<?php

declare(strict_types=1);

namespace Lexgur\GondorGains;

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
        if (preg_match('/\\\\{3,}/', $scriptClass)) {
            throw new \InvalidArgumentException("Invalid script class name: Too many consecutive backslashes.");
        }

        $scriptClass = str_replace('/', '\\', $scriptClass);

        $scriptClass = preg_replace('/\\\\{2}/', '\\', $scriptClass);

        ScriptNameValidator::validate($scriptClass);

        return $scriptClass;
    }
}
