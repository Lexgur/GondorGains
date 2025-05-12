<?php

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
            try {
                $userId = (int) $_SESSION['id'];
                $startedAt = new \DateTimeImmutable();
                $challenge = new Challenge(
                    userId: $userId,
                    startedAt: $startedAt
                );

                $this->challengeRepository->save($challenge);

                return $this->render('challenge_started.html.twig', [
                    'challenge' => $challenge,
                ]);
            } catch (\Throwable) {
                return $this->render('error.html.twig', [
                    'code' => 403,
                    'title' => 'Access restricted',
                    'message' => 'You don\'t have permission to view this content.',
                ]);
            }
        }

        return $this->render('challenge_started.html.twig');
    }
}
