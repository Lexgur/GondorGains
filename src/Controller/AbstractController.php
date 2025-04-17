<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\TemplateProvider;

#[Path('/abstract-controller')]
abstract class AbstractController
{
    public function __construct(protected TemplateProvider $templateProvider) 
    {
        $this->templateProvider = $templateProvider;
    }

    /** @param array<string, mixed> $params */
    protected function render(string $template, array $params = [], int $statusCode = 200): string
    {
        http_response_code($statusCode);
        header('Content-Type: text/html; charset=UTF-8');

        return $this->templateProvider->get()->render($template, $params);
    }

}