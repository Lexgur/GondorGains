<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Controller\AboutProjectController;
use Lexgur\GondorGains\Controller\ViewChallengeController;
use Lexgur\GondorGains\Exception\NotFoundException;
use Lexgur\GondorGains\Router;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    private Router $router;

    private string $controllerDir;

    /**
     * @throws NotFoundException
     */
    protected function setUp(): void
    {
        $this->controllerDir = __DIR__ . '/../src/Controller';
        $this->router = new Router($this->controllerDir);
        $this->router->registerControllers();
    }

    #[DataProvider('provideTestGetControllerData')]
    final public function testGetController(string $routePath, string $expectedController, array $expectedParams): void
    {
        $result = $this->router->getController($routePath);
        $controller = $result[0];
        $params = $result[1];


        $this->assertSame($expectedController, $controller);
        $this->assertSame($expectedParams, $params);
    }

    #[DataProvider('provideTestGetControllerThrowsIncorrectRoutePathException')]
    final public function testGetControllerThrowsIncorrectRoutePathException(string $routePath, string $expectedController): void
    {
        $this->expectException(NotFoundException::class);

        $controller = $this->router->getController($routePath);
        $this->assertSame($expectedController, $controller[0]);
    }

    final public function testIncorrectPathThrowsIncorrectRoutePathException(): void
    {
        $this->expectException(NotFoundException::class);

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

    /** @return array<int, array<int,mixed>> */
    public static function provideTestGetControllerData(): array
    {
        return [
            ['/about', AboutProjectController::class, []],
            ['/daily-quest/11', ViewChallengeController::class, ['id' => 11]],
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
