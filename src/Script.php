<?php

declare(strict_types=1);

namespace Lexgur\GondorGains;

use Lexgur\GondorGains\Exception\ScriptFailedToRunException;
use Lexgur\GondorGains\Script\ScriptInterface;

class Script {

    protected Container $container;

    public function __construct()
    {
        $this->container = new Container();
    }

    public function run(string $scriptClass): void
    {
        $className = '\\' . str_replace('/', '\\', $scriptClass);
        $service = $this->container->get($className);

        if (!$service instanceof ScriptInterface) {
            throw new ScriptFailedToRunException('Script does not belong to ScriptInterface');
        }
        echo $service->run();
    }
}