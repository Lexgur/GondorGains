<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Exception\ForbiddenException;
use Lexgur\GondorGains\Repository\ChallengeModelRepository;
use Lexgur\GondorGains\Service\CurrentUser;
use Lexgur\GondorGains\Service\RandomQuote;
use Lexgur\GondorGains\TemplateProvider;

#[Path('/quests')]
class QuestsController extends AbstractController
{
    private RandomQuote $randomQuote;

    private CurrentUser $currentUser;

    private ChallengeModelRepository $challengeRepository;

    public function __construct(TemplateProvider $templateProvider, RandomQuote $randomQuote, CurrentUser $currentUser, ChallengeModelRepository $challengeRepository)
    {
        parent::__construct($templateProvider);
        $this->randomQuote = $randomQuote;
        $this->currentUser = $currentUser;
        $this->challengeRepository = $challengeRepository;
    }

    public function __invoke(): string
    {
        try {
            if ($this->currentUser->isAnonymous()) {
                throw new ForbiddenException();
            }
            $userChallenges = $this->challengeRepository->fetchAllChallenges();
            $completedChallenges = [];

            foreach ($userChallenges as $userChallenge) {
                if (null !== $userChallenge->getCompletedAt()) {
                    $completedChallenges[] = [
                        'id' => $userChallenge->getChallengeId(),
                        'started_at' => $userChallenge->getStartedAt()->format('Y-m-d H:i'),
                        'completed_at' => $userChallenge->getCompletedAt()->format('Y-m-d H:i'),
                    ];
                }
            }

            return $this->render('quests.html.twig', [
                'quote' => $this->randomQuote->getQuote(),
                'message' => 'List of completed quests, Gondorian:',
                'challenges' => $completedChallenges,
                'quest' => '/daily-quest/start',
            ]);
        } catch (\Throwable) {
            return $this->render('error.html.twig', [
                'code' => 403,
                'title' => 'Access restricted',
                'message' => 'You don\'t have permission to view this content.',
            ]);
        }
    }
}
