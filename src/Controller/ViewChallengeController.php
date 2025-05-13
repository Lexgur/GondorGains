<?php

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Exception\ChallengeNotFoundException;
use Lexgur\GondorGains\Exception\ForbiddenException;
use Lexgur\GondorGains\Model\Challenge;
use Lexgur\GondorGains\Repository\ChallengeModelRepository;
use Lexgur\GondorGains\Service\CurrentUser;
use Lexgur\GondorGains\TemplateProvider;

#[Path('/daily-quest/:id')]
class ViewChallengeController extends AbstractController
{
    private CurrentUser $currentUser;

    private ChallengeModelRepository $challengeRepository;

    private Challenge $challenge;

    public function __construct(TemplateProvider $templateProvider, CurrentUser $currentUser, ChallengeModelRepository $challengeRepository, Challenge $challenge)
    {
        parent::__construct($templateProvider);
        $this->currentUser = $currentUser;
        $this->challengeRepository = $challengeRepository;
        $this->challenge = $challenge;
    }

    /**
     * @throws ChallengeNotFoundException
     * @throws ForbiddenException
     */
    public function __invoke(): string
    {
        if ($this->currentUser->isAnonymous()) {
            throw new ForbiddenException();
        }

        $user = $this->currentUser->getUser();
        $challengeId = $this->challenge->getChallengeId();
        $challenge = $this->challengeRepository->fetchById($challengeId);

        if (!$challenge) {
            throw new ChallengeNotFoundException("Challenge not found.");
        }
        if ($challenge->getUserId() !== $user->getUserId()) {
            throw new ForbiddenException();
        }

        $isCompleted = $challenge->getCompletedAt();

        $message = $isCompleted ? 'You have done a great job, completing all '.$this->calculateCompletionPercentage($challenge).'% of the challenges!' : 'You tried with honour completing '.$this->calculateCompletionPercentage($challenge).'% of the challenge. Perhaps you wish to try again today?';

        return $this->render('challenge_view.html.twig', [
            'challenge' => $challenge,
            'message' => $message,
            'isCompleted' => $isCompleted,
        ]);
    }

    private function calculateCompletionPercentage(Challenge $challenge): int
    {
        return rand(70, 100);
    }
}
