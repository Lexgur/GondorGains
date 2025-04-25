<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Exception\ForbiddenException;
use Lexgur\GondorGains\Service\RandomQuote;
use Lexgur\GondorGains\TemplateProvider;

#[Path('/weakling')]
class WeaklingController extends AbstractController
{
    private RandomQuote $randomQuote;

    public function __construct(TemplateProvider $templateProvider, RandomQuote $randomQuote)
    {
        parent::__construct($templateProvider);
        $this->randomQuote = $randomQuote;
    }

    public function __invoke(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['id'])) {
            throw new ForbiddenException();
        }
        return $this->render("weakling.html.twig", [
            'quote' => $this->randomQuote->getQuote(),
        ]);
    }
}