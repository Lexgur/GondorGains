<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

class ApplicationTest extends WebTestCase
{
    public function testBadRequestException(): void
    {
        $output = $this->request('GET', '/test/bad-request');

        $this->assertStringContainsString('Please check your information and try again.', $output['output']);
        $this->assertEquals(400, $output['status']);;
    }

    public function testUnauthorizedException(): void
    {
        $output = $this->request('GET', '/test/unauthorized');

        $this->assertStringContainsString('Please sign in', $output['output']);
        $this->assertEquals(401, $output['status']);
    }

    public function testForbiddenException(): void
    {
        $output = $this->request('GET', '/test/forbidden');

        $this->assertStringContainsString('Access restricted', $output['output']);
        $this->assertEquals(403, $output['status']);
    }

    public function testNotFoundException(): void
    {
        $output = $this->request('GET', '/test/not-found');

        $this->assertStringContainsString('Page not found', $output['output']);
        $this->assertEquals(404, $output['status']);
    }
}