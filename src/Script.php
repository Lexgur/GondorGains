<?php

declare(strict_types=1);

namespace Lexgur\GondorGains;

use Lexgur\GondorGains\Exception\ScriptFailedToRunException;
use Lexgur\GondorGains\Script\ScriptInterface;
use ReflectionClass;
use Throwable;

class Script {

    private Container $container;

    public function __construct ()
    {
        $this->container = new Container();
    }

    public function getScripts(): array
    {
        $scriptPath = __DIR__ . '/Script';

        $scripts = [];
        foreach(scandir($scriptPath) as $scriptFile){
            try {
                if (pathinfo($scriptFile, PATHINFO_EXTENSION) === 'php'){
                    $className = "Lexgur\\GondorGains\\Script\\" . pathinfo($scriptFile, PATHINFO_FILENAME);
                } if (class_exists($className)){
                  $reflection = new ReflectionClass($className);
                    if ($reflection->implementsInterface(ScriptInterface::class)){
                        $scripts[$className] = $this->container->get($className);
                    }
                }

            } catch (Throwable $e) {
                throw new ScriptFailedToRunException('Script loading failed: ' . $e->getMessage());
            }
        }
        return $scripts;
    }

}