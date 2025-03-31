<?php

declare(strict_types=1);

use Lexgur\GondorGains\Controller\UserRegisterController;
use Lexgur\GondorGains\Core\Router;
use Lexgur\GondorGains\Exception\IncorrectRoutePathException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
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
        $filePath = __DIR__.'/../src/Controller/UserRegisterController.php';
        $result = $this->router->getFullClassName($filePath);

        $this->assertSame('Lexgur\GondorGains\Controller\UserRegisterController', $result);
    }

    final public function testRegisterControllers(): void
    {
        $routes = $this->router->getRoutes();

        $this->assertNotEmpty($routes, 'No routes were registered. Ensure controllers have #[Path] attributes.');

        $expectedRoutes = [
            '/register',
        ];

        foreach ($expectedRoutes as $route) {
            $this->assertArrayHasKey($route, $routes, "Route '{$route}' was not registered.");
        }
    }

    public static function provideTestGetControllerData(): array
    {
        return [
            ['/register', UserRegisterController::class],
        ];
    }

    public static function provideTestGetControllerThrowsIncorrectRoutePathException(): array
    {
        return [
            ['/ss', UserRegisterController::class],
        ];
    }
}
