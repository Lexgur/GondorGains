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

    public function __construct(TemplateProvider $templateProvider, CurrentUser $currentUser, ChallengeModelRepository $challengeRepository)
    {
        parent::__construct($templateProvider);
        $this->currentUser = $currentUser;
        $this->challengeRepository = $challengeRepository;
    }

    /**
     * @throws ChallengeNotFoundException
     * @throws ForbiddenException
     */
    public function __invoke(int $id): string
    {
        if ($this->currentUser->isAnonymous()) {
            throw new ForbiddenException();
        }

        $user = $this->currentUser->getUser();
        $challenge = $this->challengeRepository->fetchById($id);

        if ($challenge->getUserId() !== $user->getUserId()) {
            throw new ForbiddenException();
        }

        $isCompleted = (bool) $challenge->getCompletedAt();

        return $this->render('challenge_view.html.twig', [
            'challenge' => $challenge,
            'isCompleted' => $isCompleted,
        ]);
    }
}
