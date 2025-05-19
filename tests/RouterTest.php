<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Controller\QuestsController;
use Lexgur\GondorGains\Exception\FilePathReadException;
use Lexgur\GondorGains\Exception\NotFoundException;
use Lexgur\GondorGains\Exception\RegisterControllerException;
use Lexgur\GondorGains\Router;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    private Router $router;

    private string $controllerDir;

    private string $filesystem;

    /**
     * @throws NotFoundException
     */
    protected function setUp(): void
    {
        $this->controllerDir = __DIR__ . '/../src/Controller';
        $this->filesystem = __DIR__ . '/../tmp/test';

        if (!is_dir($this->filesystem . '/RouterTest')) {
            mkdir($this->filesystem . '/RouterTest', 0777, true);
        }
        chmod($this->filesystem . '/RouterTest', 0777);

        $this->router = new Router($this->controllerDir);
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
        $this->expectException(NotFoundException::class);

        $controller = $this->router->getController($routePath);
        $this->assertSame($expectedController, $controller);
    }

    /**
     * @param array<string, string> $expectedParams
     */
    #[DataProvider('provideTestGetParametersData')]
    public function testGetParameters(string $routePath, array $expectedParams): void
    {
        $params = $this->router->getParameters($routePath);
        $this->assertSame($expectedParams, $params);
    }

    /**
     * @param array<string, string> $expectedParams
     */
    #[DataProvider('provideTestGetEmptyParametersDataArray')]
    public function testGetEmptyParametersArray(string $routePath, array $expectedParams): void
    {
        $params = $this->router->getParameters($routePath);
        $this->assertSame($expectedParams, $params);
    }

    final public function testIncorrectPathThrowsIncorrectRoutePathException(): void
    {
        $this->expectException(NotFoundException::class);

        $this->router->getController('/incorrect');
    }

    final public function testGetFullClassName(): void
    {
        $filePath = __DIR__.'/../src/Controller/QuestsController.php';
        $result = $this->router->getFullClassName($filePath);

        $this->assertSame('Lexgur\GondorGains\Controller\QuestsController', $result);
    }

    public function testGetFullClassNameThrowsFilePathReadExceptionForEmptyFile(): void
    {
        $this->expectException(FilePathReadException::class);

        $emptyFilePath = $this->filesystem . '/EmptyFile.php';
        file_put_contents($emptyFilePath, '');

        $router = new Router(__DIR__ . '/../src/Controller');
        $router->getFullClassName($emptyFilePath);
    }


    public function testGetFullClassNameThrowsNotFoundExceptionForNoClass(): void
    {
        $this->expectException(NotFoundException::class);

        $dir = $this->filesystem . '/RouterTest';
        $filePath = $dir . '/NoClassFile.php';
        file_put_contents($filePath, "<?php\n\nnamespace Lexgur\\GondorGains\\Controller;\n\ntrait NoClass{}\n");

        $router = new Router($dir);
        $router->getFullClassName($filePath);
    }

    final public function testRegisterControllers(): void
    {
        $routes = $this->router->getRoutes();

        $this->assertNotEmpty($routes);

        $expectedRoutes = [
            '/quests',
        ];

        foreach ($expectedRoutes as $route) {
            $this->assertArrayHasKey($route, $routes);
        }
    }

    public function testRegisterControllerThrowsExceptionFromInvalidClass(): void
    {
        $this->expectException(RegisterControllerException::class);

        $testControllerDir = $this->filesystem . '/RouterTest';
        file_put_contents($testControllerDir . '/InvalidController.php', "<?php\nnamespace Lexgur\\GondorGains\\Controller;\nclass InvalidController {}");

        $router = new Router($testControllerDir);
        $router->registerControllers();
    }

    /** @return array<int, array<int,string>> */
    public static function provideTestGetControllerData(): array
    {
        return [
            ['/quests', QuestsController::class],
        ];
    }

    /** @return array<int, array<int, string>> */
    public static function provideTestGetControllerThrowsIncorrectRoutePathException(): array
    {
        return [
            ['/incorrectPath', QuestsController::class],
        ];
    }

    /** @return array<int, array{string, array<string, int>}> */
    public static function provideTestGetParametersData(): array
    {
        return [
            ['/daily-quest/11', ['id' => 11]],
            ['/daily-quest/7', ['id' => 7]],
        ];
    }

    /** @return array<int, array{string, array<string, int>}> */
    public static function provideTestGetEmptyParametersDataArray(): array
    {
        return [
            ['/daily-quest/', []],
        ];
    }
}
