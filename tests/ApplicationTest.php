<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

class ApplicationTest extends WebTestCase
{
    public function testBadRequestException(): void
    {
        $output = $this->request('GET', '/test/bad-request');

        $this->assertStringContainsString('Please check your information and try again.', $output);
        $this->assertStringContainsString('400', $output);
    }

    public function testUnauthorizedException(): void
    {
        $output = $this->request('GET', '/test/unauthorized');

        $this->assertStringContainsString('Please sign in', $output);
        $this->assertStringContainsString('401', $output);
    }

    public function testForbiddenException(): void
    {
        $output = $this->request('GET', '/test/forbidden');

        $this->assertStringContainsString('Access restricted', $output);
        $this->assertStringContainsString('403', $output);
    }

    public function testNotFoundException(): void
    {
        $output = $this->request('GET', '/test/not-found');

        $this->assertStringContainsString('Page not found', $output);
        $this->assertStringContainsString('404', $output);
    }
}