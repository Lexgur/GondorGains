<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Repository\UserModelRepository;
use Lexgur\GondorGains\TemplateProvider;

#[Path('/dashboard')]
class DashboardController extends AbstractController
{
    private UserModelRepository $userRepository;

    public function __construct(UserModelRepository $userRepository, TemplateProvider $templateProvider)
    {
        parent::__construct($templateProvider);
        $this->userRepository = $userRepository;
    }

    public function __invoke(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['id'])) {
            return $this->render('login.html.twig', [
                'error' => 'You must be logged in to view the dashboard.'
            ]);
        }

        try {
            $userId = (int)$_SESSION['id'];

            $user = $this->userRepository->fetchById($userId);
            if (!$user) {
                throw new \RuntimeException("User not found.");
            }

            $completedPercentageAverage = 0;
            $totalQuests = 0;

            return $this->render('dashboard.html.twig', [
                'message' => sprintf(
                    "Greetings, %s, on average you have completed %d of your %d quests!",
                    $user->getUsername(),
                    $completedPercentageAverage,
                    $totalQuests
                ),
                'quote' => '“Deeds will not be less valiant because they are unpraised.” — Aragorn'
            ]);

        } catch (\Throwable) {
            return $this->render('error.html.twig', [
                'code' => 500,
                'title' => "We're having some trouble",
                'message' => 'Our team has been notified. Please try again later.',
            ]);
        }
    }
}
