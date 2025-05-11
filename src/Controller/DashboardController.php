<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Exception\ForbiddenException;
use Lexgur\GondorGains\Exception\NotFoundException;
use Lexgur\GondorGains\Exception\UserNotFoundException;
use Lexgur\GondorGains\Repository\ChallengeModelRepository;
use Lexgur\GondorGains\Repository\UserModelRepository;
use Lexgur\GondorGains\Service\CurrentUser;
use Lexgur\GondorGains\Service\RandomQuote;
use Lexgur\GondorGains\TemplateProvider;

#[Path('/dashboard')]
class DashboardController extends AbstractController
{
    private UserModelRepository $userRepository;

    private RandomQuote $randomQuote;

    private ChallengeModelRepository $challengeRepository;

    private CurrentUser $currentUser;

    public function __construct(UserModelRepository $userRepository, TemplateProvider $templateProvider, RandomQuote $randomQuote, ChallengeModelRepository $challengeRepository, CurrentUser $currentUser)
    {
        parent::__construct($templateProvider);
        $this->userRepository = $userRepository;
        $this->randomQuote = $randomQuote;
        $this->challengeRepository = $challengeRepository;
        $this->currentUser = $currentUser;
    }

    /**
     * @throws ForbiddenException
     * @throws NotFoundException|UserNotFoundException
     */
    public function __invoke(): string
    {
        try {
            if ($this->currentUser->isAnonymous()) {
                throw new ForbiddenException();
            }
            $userId = (int) $_SESSION['id'];
            $user = $this->userRepository->fetchById($userId);
            $userChallenges = $this->challengeRepository->fetchAllChallenges();

            $completedChallenges = array_filter($userChallenges, fn ($challenge) => $challenge->getUserId() === $userId && null !== $challenge->getCompletedAt());
            $completedAverage = count($completedChallenges);
            $totalQuests = count($userChallenges);

            return $this->render('dashboard.html.twig', [
                'message' => sprintf(
                    'Greetings, %s, on average you have completed %d of your %d quests!',
                    $user->getUsername(),
                    $completedAverage,
                    $totalQuests
                ),
                'quote' => $this->randomQuote->getQuote(),
                'begin' => $this->handleRedirect($totalQuests),
            ]);
        } catch (\Throwable) {
            return $this->render('error.html.twig', [
                'code' => 403,
                'title' => 'Access restricted',
                'message' => 'You don\'t have permission to view this content.',
            ]);
        }
    }

    private function handleRedirect(int $totalQuests): string
    {
        if (0 === $totalQuests) {
            return '/weakling';
        }

        return '/quests';
    }
}
