<?php

namespace App\Service;

use App\Repository\MentionRepository;
use \App\Entity\Mention;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MentionService
{
    private $mentionRepository;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    private $userService;

    public function __construct(
        MentionRepository $mentionRepository,
        GoogleService $googleService,
        ParameterBagInterface $parameterBag,
        UserService $userService
    ) {
        $this->mentionRepository = $mentionRepository;
        $this->googleService = $googleService;
        $this->parameterBag = $parameterBag;
        $this->userService = $userService;
    }

    /**
     * @param integer $mentionId
     * @return Mention|null
     */
    public function find($mentionId) {
        return $this->mentionRepository->find($mentionId);
    }

    public function count() {
        return $this->mentionRepository->count([]);
    }

    public function countMentionByDate($year, $month, $day, $hour, $groupBy) {
        if (!isset($year)) {
            $year = new \DateTime('Y');
        }

        return $this->mentionRepository->getMentionForPeriod($year, $month, $day, $hour, $groupBy);
    }

    public function analyse($mentionId) {
        $mention = $this->find($mentionId);

        if ($mention && !in_array($mention->getUserId(), $this->userService->getBannedUserTwitterIds())) {
            $sentiment = $this->googleService->sentimentAnalysis($mention->getContentRaw());

            $mention->setSentimentMagnitude($sentiment['magnitude']);
            $mention->setSentimentScore($sentiment['score']);

            $this->mentionRepository->save($mention);
        }
    }
}
