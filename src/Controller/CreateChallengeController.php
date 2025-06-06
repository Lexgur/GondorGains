<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Exception\ForbiddenException;
use Lexgur\GondorGains\Repository\ChallengeModelRepository;
use Lexgur\GondorGains\Service\ChallengeCreatorService;
use Lexgur\GondorGains\Service\CurrentUser;
use Lexgur\GondorGains\TemplateProvider;

#[Path('/daily-quest/start')]
class CreateChallengeController extends AbstractController
{
    private CurrentUser $currentUser;
    private ChallengeModelRepository $challengeRepository;

    private ChallengeCreatorService $challengeCreator;

    public function __construct(TemplateProvider $templateProvider, CurrentUser $currentUser, ChallengeModelRepository $challengeRepository, ChallengeCreatorService $challengeCreator)
    {
        parent::__construct($templateProvider);
        $this->currentUser = $currentUser;
        $this->challengeRepository = $challengeRepository;
        $this->challengeCreator = $challengeCreator;
    }

    public function __invoke(): string
    {
        if ($this->currentUser->isAnonymous()) {
            throw new ForbiddenException();
        }

        if ($this->isPostRequest()) {
            if ('challenge-complete' === $_POST['action']) {
                $user = $this->currentUser->getUser();
                $userId = $user->getUserId();
                $challenge = $this->challengeCreator->createChallenge($userId);
                $this->challengeRepository->save($challenge);
                $this->redirect('/quests');
            }
        }

        return $this->render('challenge_started.html.twig');
    }
}
