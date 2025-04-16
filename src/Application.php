<?php 

declare(strict_types=1);

namespace Lexgur\GondorGains;

class Application
{
    private Container $container;

    private array $config;

    private Router $router;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../config.php';
        $this->container = new Container($this->config);
        $this->router = new Router();
    }

    public function run() : void
    {
        $this->router->registerControllers();
        $routePath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        $controllerClass = $this->router->getController($routePath);

        $controller = $this->container->get($controllerClass);

        print $controller();
    }
}