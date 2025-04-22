<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Exception\ForbiddenException;
use Lexgur\GondorGains\TemplateProvider;

#[Path('/quests')]
class QuestsController extends AbstractController
{
    public function __construct(TemplateProvider $templateProvider)
    {
        parent::__construct($templateProvider);
    }

    public function __invoke(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['id'])) {
            throw new ForbiddenException();
        }
        return $this->render("quests.html.twig");
    }
}