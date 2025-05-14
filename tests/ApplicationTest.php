<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests;

class ApplicationTest extends WebTestCase
{
    public function testBadRequestException(): void
    {
        $output = $this->request('GET', '/test/bad-request');
        $statusCode = http_response_code();

        $this->assertStringContainsString('Please check your information and try again.', $output);
        $this->assertEquals(400, $statusCode);
    }

    public function testUnauthorizedException(): void
    {
        $output = $this->request('GET', '/test/unauthorized');
        $statusCode = http_response_code();

        $this->assertStringContainsString('Please sign in', $output);
        $this->assertEquals(401, $statusCode);
    }

    public function testForbiddenException(): void
    {
        $output = $this->request('GET', '/test/forbidden');
        $statusCode = http_response_code();

        $this->assertStringContainsString('Access restricted', $output);
        $this->assertEquals(403, $statusCode);
    }

    public function testNotFoundException(): void
    {
        $output = $this->request('GET', '/test/not-found');
        $statusCode = http_response_code();

        $this->assertStringContainsString('Page not found', $output);
        $this->assertEquals(404, $statusCode);
    }

    public function testEmptyPathReturnsLoginController(): void
    {
        $output = $this->request('GET', '');
        $statusCode = http_response_code();

        $this->assertStringContainsString('Login', $output);
        $this->assertEquals(200, $statusCode);
    }
}