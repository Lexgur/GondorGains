<?php

declare(strict_types=1);

namespace Lexgur\GondorGains;

use Lexgur\GondorGains\Controller\ErrorController;
use Lexgur\GondorGains\Controller\LoginUserController;
use Lexgur\GondorGains\Service\ControllerParameterResolver;

class Application
{
    private Container $container;

    /** @var array<string> */
    private array $config;

    private Router $router;

    private ControllerParameterResolver $parameterResolver;

    public function __construct()
    {
        $this->config = require __DIR__.'/../config.php';
        $this->container = new Container($this->config);
        $this->router = $this->container->get(Router::class);
        $this->parameterResolver = $this->container->get(ControllerParameterResolver::class);
    }

    public function run(): void
    {
        try {
            $this->router->registerControllers();
            $routePath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

            if (empty($routePath) || '/' === $routePath) {
                $controller = $this->container->get(LoginUserController::class);
                $params = [];
            } else {
                $controllerClass = $this->router->getController($routePath);
                $params = $this->router->getParameters($routePath);
                $controller = $this->container->get($controllerClass);
            }
            http_response_code(200);
            $invokerArgs = $this->parameterResolver->resolveParameters($controller, $params);
            echo $controller(...$invokerArgs);

        } catch (\Throwable $error) {
            $errorController = $this->container->get(ErrorController::class);
            echo $errorController($error);
        }
    }
}
