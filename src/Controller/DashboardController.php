<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Exception\ForbiddenException;
use Lexgur\GondorGains\Exception\NotFoundException;
use Lexgur\GondorGains\Exception\UserNotFoundException;
use Lexgur\GondorGains\Repository\UserModelRepository;
use Lexgur\GondorGains\Service\RandomQuote;
use Lexgur\GondorGains\TemplateProvider;

#[Path('/dashboard')]
class DashboardController extends AbstractController
{
    private UserModelRepository $userRepository;

    private RandomQuote $randomQuote;

    public function __construct(UserModelRepository $userRepository, TemplateProvider $templateProvider, RandomQuote $randomQuote)
    {
        parent::__construct($templateProvider);
        $this->userRepository = $userRepository;
        $this->randomQuote = $randomQuote;

    }
    /**
     * @throws ForbiddenException
     * @throws UserNotFoundException|NotFoundException
     */
    public function __invoke(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['id'])) {
            throw new ForbiddenException();
        }
        $userId = (int)$_SESSION['id'];
        $user = $this->userRepository->fetchById($userId);
        if (!$user) {
            throw new NotFoundException("User not found.");
        }
        $completedAverage = 0;
        $totalQuests = 0;

        return $this->render('dashboard.html.twig', [
            'message' => sprintf(
                "Greetings, %s, on average you have completed %d of your %d quests!",
                $user->getUsername(),
                $completedAverage,
                $totalQuests
                ),
            'quote' => $this->randomQuote->getQuote(),
            ]);
    }
}
