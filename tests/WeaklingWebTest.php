<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Container;

class WeaklingWebTest extends WebTestCase
{
    public function setUp(): void
    {
        $_ENV['IS_WEB_TEST'] = 'true';

        $config = require __DIR__.'/../config.php';
        $container = new Container($config);

        parent::setUp();
    }

    public function tearDown(): void
    {
        unset($_ENV['IS_WEB_TEST']);

        session_unset();
        parent::tearDown();
    }

    public function testSuccessfulPath(): void
    {
        $dashboardOutput = $this->request('GET', '/weakling');

        $this->assertStringContainsString('have permission to view', $dashboardOutput);
        $this->assertSame(403, $GLOBALS['_LAST_HTTP_CODE']);
    }
}
