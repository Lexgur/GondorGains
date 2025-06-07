<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;
use Lexgur\GondorGains\Exception\ForbiddenException;
use Lexgur\GondorGains\Exception\NotEnoughExercisesException;
use Lexgur\GondorGains\Repository\ChallengeModelRepository;
use Lexgur\GondorGains\Service\ChallengeCreatorService;
use Lexgur\GondorGains\Service\CurrentUser;
use Lexgur\GondorGains\TemplateProvider;
use Random\RandomException;

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

    /**
     * @throws ForbiddenException
     * @throws RandomException
     */
    public function __invoke(): string
    {
        if ($this->currentUser->isAnonymous()) {
            throw new ForbiddenException();
        }

        $exercises = [];

        if ($this->isPostRequest()) {
            if ('challenge-complete' === $_POST['action']) {
                try {
                    $user = $this->currentUser->getUser();
                    $userId = $user->getUserId();
                    $challenge = $this->challengeCreator->createChallenge($userId);
                    $challenge->setCompletedAt(new \DateTime());
                    $this->challengeRepository->save($challenge);

                    $this->redirect('/quests');
                } catch (NotEnoughExercisesException|RandomException $e) {
                    return $this->render('error.html.twig', [
                        'message' => 'Challenge could not be created: '.$e->getMessage(),
                    ]);
                }
            }
        } else {
            $exercises = $this->challengeCreator->fetchExercisesForChallenge();
        }

        return $this->render('challenge_started.html.twig', [
            'exercises' => $exercises,
        ]);
    }
}
