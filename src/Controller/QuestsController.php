<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Exception\ForbiddenException;
use Lexgur\GondorGains\Service\CurrentUser;
use Lexgur\GondorGains\Service\RandomQuote;
use Lexgur\GondorGains\TemplateProvider;

#[Path('/quests')]
class QuestsController extends AbstractController
{
    private RandomQuote $randomQuote;

    private CurrentUser $currentUser;

    public function __construct(TemplateProvider $templateProvider, RandomQuote $randomQuote, CurrentUser $currentUser)
    {
        parent::__construct($templateProvider);
        $this->randomQuote = $randomQuote;
        $this->currentUser = $currentUser;
    }

    public function __invoke(): string
    {
        if ($this->currentUser->isAnonymous()) {
            throw new ForbiddenException();
        }
        return $this->render("quests.html.twig", [
            'quote' => $this->randomQuote->getQuote(),
        ]);
    }
}