<?php

namespace App\Service;


use App\Repository\MentionRepository;
use \App\Entity\Mention;

class MentionService
{
    private $mentionRepository;

    public function __construct(
        MentionRepository $mentionRepository,
        GoogleService $googleService
    ) {
        $this->mentionRepository = $mentionRepository;
        $this->googleService = $googleService;
    }

    /**
     * @param integer $mentionId
     * @return Mention|null
     */
    public function find($mentionId) {
        return $this->mentionRepository->find($mentionId);
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
