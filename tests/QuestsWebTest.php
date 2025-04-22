<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

use Lexgur\GondorGains\Exception\ForbiddenException;

class QuestsWebTest extends WebTestCase
{
    public function setUp(): void
    {
        $_ENV['IS_WEB_TEST'] = 'true';
        parent::setUp();
    }

    public function tearDown(): void
    {
        session_unset();
        parent::tearDown();
    }

    public function testAnonymousAccessDenied(): void
    {
        $dashboardOutput = $this->request('GET', '/quests');

        $this->assertStringContainsString("Access restricted", $dashboardOutput['output']);
        $this->assertEquals(403, $dashboardOutput['status']);
    }
}
