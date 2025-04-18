<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Repository\UserModelRepository;
use Lexgur\GondorGains\TemplateProvider;
use Lexgur\GondorGains\Validation\UserModelValidator;


#[Path('/abstract-controller')]
abstract class AbstractController
{
    public TemplateProvider $templateProvider;
    public UserModelValidator $validator;
    public UserModelRepository $repository;

    public function __construct(TemplateProvider $templateProvider, UserModelValidator $validator, UserModelRepository $repository)
    {
        $this->templateProvider = $templateProvider;
        $this->validator = $validator;
        $this->repository = $repository;
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

}