<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Exception\ForbiddenException;
use Lexgur\GondorGains\Service\RandomQuote;
use Lexgur\GondorGains\Service\Session;
use Lexgur\GondorGains\TemplateProvider;

#[Path('/quests')]
class QuestsController extends AbstractController
{
    private RandomQuote $randomQuote;

    private Session $session;

    public function __construct(TemplateProvider $templateProvider, RandomQuote $randomQuote, Session $session)
    {
        parent::__construct($templateProvider);
        $this->randomQuote = $randomQuote;
        $this->session = $session;
    }

    public function __invoke(): string
    {
        if (!$this->session->hasStarted()) {
            throw new ForbiddenException();
        }
        return $this->render("quests.html.twig", [
            'quote' => $this->randomQuote->getQuote(),
        ]);
    }
}