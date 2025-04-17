<?php

declare(strict_types=1);

namespace Lexgur\GondorGains;

use Lexgur\GondorGains\Controller\ErrorController;
use Lexgur\GondorGains\Controller\AboutProjectController;

class Application
{
    private Container $container;

    /** @var array<string> */
    private array $config;

    private Router $router;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../config.php';
        $this->container = new Container($this->config);
        $this->router = $this->container->get(Router::class);
    }

    public function run(): void
    {
        try {
            $this->router->registerControllers();
            $routePath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

            if (empty($routePath) || $routePath === '/') {
                $controller = $this->container->get(AboutProjectController::class);
            } else {
                $controllerClass = $this->router->getController($routePath);
                $controller = $this->container->get($controllerClass);
            }
            http_response_code(200);
            print $controller();

        } catch (\Throwable $error) {
            $errorController = $this->container->get(ErrorController::class);
            print $errorController($error);
        }
    }
}