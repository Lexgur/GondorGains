<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests\Script;

use Lexgur\GondorGains\Container;
use Lexgur\GondorGains\Script\RunMigrationsScript;
use Lexgur\GondorGains\Tests\Script\RunMigrationsScriptTest\FailedMigrate\FailedMigration;
use Lexgur\GondorGains\Tests\Script\RunMigrationsScriptTest\RunOrder\FirstMigration;
use Lexgur\GondorGains\Tests\Script\RunMigrationsScriptTest\RunOrder\LastMigration;
use Lexgur\GondorGains\Tests\Script\RunMigrationsScriptTest\RunOrder\SecondMigration;
use Lexgur\GondorGains\Tests\Script\RunMigrationsScriptTest\RunOrder\ThirdMigration;
use PHPUnit\Framework\TestCase;

class RunMigrationsScriptTest extends TestCase
{
    private array $testConfig;
    private string $migratedRegistryPath;
    public function setUp(): void
    {
        $this->testConfig = require __DIR__ . '/../../config.php';
        $this->migratedRegistryPath = uniqid('', true);
        mkdir(str_replace( $this->migratedRegistryPath .'.json', '', $this->getMigratedRegistryPath()), recursive: true);
    }

    public function tearDown(): void
    {
        unlink($this->getMigratedRegistryPath());
    }

    public function testRunOrder(): void
    {
        $runMigrationsScript = $this->getRunMigrationsScript(__DIR__ . '/RunMigrationsScriptTest/RunOrder');

        $this->expectOutputString(
            FirstMigration::class . PHP_EOL
            . SecondMigration::class . PHP_EOL
            . ThirdMigration::class . PHP_EOL
            . LastMigration::class . PHP_EOL
        );
        $this->assertEquals(0, $runMigrationsScript->run());
    }

    public function testFailedMigrate(): void
    {
        $runMigrationsScript = $this->getRunMigrationsScript(__DIR__ . '/RunMigrationsScriptTest/FailedMigrate');

        $this->expectOutputString(
            RunMigrationsScriptTest\FailedMigrate\FirstMigration::class . PHP_EOL
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(sprintf('%s has failed', FailedMigration::class));

        $runMigrationsScript->run();
    }

    public function testRunOnce(): void
    {
        $runMigrationsScript = $this->getRunMigrationsScript(__DIR__ . '/RunMigrationsScriptTest/RunOnce');

        $this->expectOutputString(
            RunMigrationsScriptTest\RunOnce\FirstMigration::class . PHP_EOL
            . RunMigrationsScriptTest\RunOnce\LastMigration::class . PHP_EOL
            . "No pending migrations found." . PHP_EOL
        );

        $this->assertEquals(0, $runMigrationsScript->run());
        $this->assertEquals(1, $runMigrationsScript->run());
    }

    private function getRunMigrationsScript(string $directory): RunMigrationsScript
    {
        $container = new Container(
            [
                'directory' => $directory,
                'migratedRegistryPath' => $this->getMigratedRegistryPath(),
            ]
        );

        return $container->get(RunMigrationsScript::class);
    }

    private function getMigratedRegistryPath(): string
    {
        $testClass = self::class;
        $test = explode("\\", $testClass);
        $testCase = end($test);

        return $this->testConfig['filesystem'] . $testCase . '/' . $this->name() . '/' . $this->migratedRegistryPath . '.json';
    }

}