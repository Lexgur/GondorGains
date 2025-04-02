<?php

declare(strict_types=1);

namespace Lexgur\GondorGains;

use Lexgur\GondorGains\Exception\ScriptFailedToRunException;
use Lexgur\GondorGains\Script\ScriptInterface;
use Lexgur\GondorGains\Validation\ScriptNameValidation;
use Throwable;

class Script {

    protected Container $container;

    public function __construct()
    {
        $this->container = new Container();
    }

    public function run(string $scriptClass): int
    {
        try {

        ScriptNameValidation::validate($scriptClass);

        $className = $this->getClassName($scriptClass);
        $service = $this->container->get($className);
        if (!$service instanceof ScriptInterface) {
            throw new ScriptFailedToRunException('Script not found');
        }
            return $service->run();
        } catch (Throwable $e) {
            throw new ScriptFailedToRunException($e->getMessage());
        }
    }

    private function getClassName(string $scriptClass): string
    {
        return '\\' . str_replace('/', '\\', $scriptClass);
    }
}