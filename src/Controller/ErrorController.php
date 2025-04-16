<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Exception\BadRequestException;
use Lexgur\GondorGains\Exception\UnauthorizedException;
use Lexgur\GondorGains\Exception\ForbiddenException;
use Lexgur\GondorGains\Exception\NotFoundException;
use Lexgur\GondorGains\TemplateProvider;

#[Path('/error')]
class ErrorController
{
    public function __construct(private TemplateProvider $templateProvider) {}

    public function __invoke(\Throwable $e): string
    {
        $params = match (true) {
            $e instanceof BadRequestException => [
                'code' => 400,
                'title' => 'Oops! Something went wrong',
                'message' => 'Please check your information and try again.',
            ],
            $e instanceof UnauthorizedException => [
                'code' => 401,
                'title' => 'Please sign in',
                'message' => 'You need to sign in to access this page.',
            ],
            $e instanceof ForbiddenException => [
                'code' => 403,
                'title' => 'Access restricted',
                'message' => 'You don\'t have permission to view this content.',
            ],
            $e instanceof NotFoundException => [
                'code' => 404,
                'title' => 'Page not found',
                'message' => 'We couldn\'t find what you\'re looking for.',
            ],
            default => [
                'code' => 500,
                'title' => 'We\'re having some trouble',
                'message' => 'Our team has been notified. Please try again later.',
            ],
        };
        return $this->renderError($params);
    }

    private function renderError(array $params): string
    {
        http_response_code($params['code']);
        header('Content-Type: text/html; charset=UTF-8');
        
        return $this->templateProvider->get()->render('error.html.twig', $params);
    }
}