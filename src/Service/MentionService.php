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
        $mention->setStatus(Mention::MENTION_STATUS_SCANNED);
        if (
            $mention &&
            !in_array($mention->getUserId(), $this->userService->getBannedUserTwitterIds()) &&
            !$this->isMentionContainBannedTerm($mention->getContentRaw())
        ) {
            $sentiment = $this->googleService->sentimentAnalysis($mention->getContentRaw());

            $mention->setSentimentMagnitude($sentiment['magnitude']);
            $mention->setSentimentScore($sentiment['score']);
            $mention->setStatus(Mention::MENTION_STATUS_ANALYSED);
        }
        $this->mentionRepository->save($mention);
    }

    /**
     * Strpos for an array
     *
     * @param $haystack
     * @param array $needles
     * @param int $offset
     * @return bool|mixed
     */
    private function strposa($haystack, $needles = [], $offset = 0) {
        $chr = [];
        foreach($needles as $needle) {
            $res = strpos($haystack, $needle, $offset);
            if ($res !== false) $chr[$needle] = $res;
        }

        if(empty($chr)) return false;

        return min($chr);
    }

    /**
     * @param $mentionContentRaw
     * @return bool|mixed
     */
    public function isMentionContainBannedTerm($mentionContentRaw) {
        return $this->strposa($mentionContentRaw, Mention::MENTION_BANNED_TERMS);
    }
}
