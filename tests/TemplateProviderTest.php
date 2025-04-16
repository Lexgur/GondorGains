<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\TemplateProvider;
use Lexgur\GondorGains\Container;
use PHPUnit\Framework\TestCase;

class TemplateProviderTest extends TestCase
{
    final public function testContainerCreatesTwigEnvironmentAndRendersTemplate(): void
    {

        $config = require __DIR__ . '/../config.php';
        $container = new Container($config);
        $templateProvider = $container->get(TemplateProvider::class);

        $this->assertTrue($container->has(TemplateProvider::class));

        $this->assertInstanceOf(TemplateProvider::class, $templateProvider);

        $output = $templateProvider->get()->render('test.html.twig', ['name' => 'World']);

        $this->assertSame('Hello, World!', $output);
    }
    
}