<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Exception\ForbiddenException;
use Lexgur\GondorGains\Model\Challenge;
use Lexgur\GondorGains\Repository\ChallengeModelRepository;
use Lexgur\GondorGains\Service\CurrentUser;
use Lexgur\GondorGains\TemplateProvider;

#[Path('/daily-quest/start')]
class CreateChallengeController extends AbstractController
{
    private CurrentUser $currentUser;
    private ChallengeModelRepository $challengeRepository;

    public function __construct(TemplateProvider $templateProvider, CurrentUser $currentUser, ChallengeModelRepository $challengeRepository)
    {
        parent::__construct($templateProvider);
        $this->currentUser = $currentUser;
        $this->challengeRepository = $challengeRepository;
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
                $startedAt = new \DateTimeImmutable();

                $challenge = new Challenge(
                    userId: $userId,
                    startedAt: $startedAt
                );
                $challenge->setCompletedAt(new \DateTimeImmutable());
                $this->challengeRepository->save($challenge);
                $this->redirect('/quests');
            }
        }

        return $this->render('challenge_started.html.twig');
    }
}
