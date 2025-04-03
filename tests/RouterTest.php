<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Controller\AboutProjectController;
use Lexgur\GondorGains\Exception\IncorrectRoutePathException;
use Lexgur\GondorGains\Router;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    private Router $router;

    /**
     * @throws IncorrectRoutePathException
     */
    protected function setUp(): void
    {
        $this->router = new Router();
        $this->router->registerControllers();
    }

    #[DataProvider('provideTestGetControllerData')]
    final public function testGetController(string $routePath, string $expectedController): void
    {
        $controller = $this->router->getController($routePath);
        $this->assertSame($expectedController, $controller);
    }

    #[DataProvider('provideTestGetControllerThrowsIncorrectRoutePathException')]
    final public function testGetControllerThrowsIncorrectRoutePathException(string $routePath, string $expectedController): void
    {
        $this->expectException(IncorrectRoutePathException::class);

        $controller = $this->router->getController($routePath);
        $this->assertSame($expectedController, $controller);
    }

    final public function testIncorrectPathThrowsIncorrectRoutePathException(): void
    {
        $this->expectException(IncorrectRoutePathException::class);

        $this->router->getController('/incorrect');
    }

    final public function testGetFullClassName(): void
    {
        $filePath = __DIR__.'/../src/Controller/AboutProjectController.php';
        $result = $this->router->getFullClassName($filePath);

        $this->assertSame('Lexgur\GondorGains\Controller\AboutProjectController', $result);
    }

    final public function testRegisterControllers(): void
    {
        $routes = $this->router->getRoutes();

        $this->assertNotEmpty($routes);

        $expectedRoutes = [
            '/about',
        ];

        foreach ($expectedRoutes as $route) {
            $this->assertArrayHasKey($route, $routes);
        }
    }

    /** @return array<int, array<int,string>> */
    public static function provideTestGetControllerData(): array
    {
        return [
            ['/about', AboutProjectController::class],
        ];
    }

    /** @return array<int, array<int, string>> */
    public static function provideTestGetControllerThrowsIncorrectRoutePathException(): array
    {
        return [
            ['/incorrectPath', AboutProjectController::class],
        ];
    }
}
