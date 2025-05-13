<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\TemplateProvider;

#[Path('/abstract-controller')]
abstract class AbstractController
{
    private TemplateProvider $templateProvider;

    public function __construct(TemplateProvider $templateProvider)
    {
        $this->templateProvider = $templateProvider;
    }

    protected function isPostRequest(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /** @param array<string, mixed> $params */
    protected function render(string $template, array $params = [], int $statusCode = 200): string
    {
        http_response_code($statusCode);
        header('Content-Type: text/html; charset=UTF-8');

        return $this->templateProvider->get()->render($template, $params);
    }

    public function redirect(string $path): string
    {
        header('Location: ' . $path, true, 302);
        return '';
    }
}