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
        ScriptNameValidator::validate($scriptClass);

        return '\\'.str_replace('/', '\\', $scriptClass);
    }
}
