<?php

declare(strict_types=1);

namespace Lexgur\GondorGains;

use Lexgur\GondorGains\Exception\IncorrectScriptNameException;
use Lexgur\GondorGains\Exception\ScriptFailedToRunException;
use Lexgur\GondorGains\Script\ScriptInterface;
use Lexgur\GondorGains\Validation\ScriptNameValidator;

class Script
{
    private Container $container;

    private ScriptNameValidator $validator;

    /** @var array<string> */
    private array $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../config.php';
        $this->container = new Container($this->config);
        $this->validator = $this->container->get(ScriptNameValidator::class);
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

            throw new IncorrectScriptNameException("Consecutive forward slashes are not allowed.");
        }
        if (str_contains($scriptClass, '\\') && str_contains($scriptClass, '/')) {

            throw new IncorrectScriptNameException("Mixed separators (\\ and /) are not allowed.");
        }

        $scriptClass = preg_replace('/\\\+/', '\\', $scriptClass);
        $scriptClass = str_replace('/', '\\', $scriptClass);

        $this->validator->validate($scriptClass);

        return $scriptClass;
    }
}
