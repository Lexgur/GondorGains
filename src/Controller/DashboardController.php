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

    private array $quotes;

    public function __construct(UserModelRepository $userRepository, TemplateProvider $templateProvider, array $quotes = null)
    {
        parent::__construct($templateProvider);
        $this->userRepository = $userRepository;

        $this->quotes = $quotes ?? [
            '“All we have to decide is what to do with the time that is given us.” — Gandalf',
            '“Even the smallest person can change the course of the future.” — Galadriel',
            '“There is some good in this world, and it’s worth fighting for.” — Samwise Gamgee',
            '“Deeds will not be less valiant because they are unpraised.” — Aragorn',
            '“I would have gone with you to the end, into the very fires of Mordor.” — Frodo',
            '“The world is indeed full of peril, and in it there are many dark places; but still there is much that is fair.” — Haldir',
        ];
    }

    public function __invoke(): string
    {
        try {

            if (!isset($_SESSION['id'])) {
                return $this->render('login.html.twig', [
                    'error' => 'You must be logged in to view the dashboard.'
                ]);
            }

            $userId = (int)$_SESSION['id'];

            $user = $this->userRepository->fetchById($userId);
            if (!$user) {
                throw new \RuntimeException("User with ID $userId not found.");
            }

            $completedPercentageAverage = 0;
            $totalQuests = 0;
            $username = $user->getUsername();

            return $this->render('dashboard.html.twig', [
                'message' => sprintf(
                    "Greetings, %s, on average you have completed %d of your %d quests!",
                    $username,
                    $completedPercentageAverage,
                    $totalQuests
                ),
                'quote' => $this->quotes[array_rand($this->quotes)]
            ]);

        } catch (\Throwable) {
            return $this->render('login.html.twig', [
                'error' => 'An error occurred while loading the dashboard. Please try again later.'
            ]);
        }
    }
}
