<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

class DashboardWebTest extends WebTestCase
{
    public function testSuccessfulRender(): void
    {
        $_SESSION['id'] = 1;
        $output = $this->request('GET', '/dashboard');

        $this->assertStringContainsString('Greetings, gondorian', $output);
    }
}
