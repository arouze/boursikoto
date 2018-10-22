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

    public function __construct(
        MentionRepository $mentionRepository,
        GoogleService $googleService,
        ParameterBagInterface $parameterBag
    ) {
        $this->mentionRepository = $mentionRepository;
        $this->googleService = $googleService;
        $this->parameterBag = $parameterBag;
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

    public function countMentionByDate($year, $month, $day, $hour) {
        if (!isset($year)) {
            $year = new DateTime('Y');
        }

        if (!isset($month)) {
            return $this->mentionRepository->getMentionForYear($year);
        }

        if (isset($month) && !isset($day)) {
            return $this->mentionRepository->getMentionForMonth($year, $month);
        }

        if (isset($day) && ! isset($hour)) {
            return $this->mentionRepository->getMentionForDay($year, $month, $day);
        }

        if (isset($hour)) {
            return $this->mentionRepository->getMentionForHour($year, $month, $day, $hour);
        }
    }

    public function analyse($mentionId) {
        $mention = $this->find($mentionId);

        if ($mention) {
            $sentiment = $this->googleService->sentimentAnalysis($mention->getContentRaw());

            dump($sentiment);

            $mention->setSentimentMagnitude($sentiment['magnitude']);
            $mention->setSentimentScore($sentiment['score']);

            $this->mentionRepository->save($mention);
        }
    }
}
