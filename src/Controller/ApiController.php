<?php

namespace App\Controller;


use App\Service\MentionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends AbstractController
{
    private $mentionService;

    public function __construct(MentionService $mentionService)
    {
        $this->mentionService = $mentionService;
    }

    public function tweetCount($year, $month = null, $day = null, $hour = null) {
        return new JsonResponse($this->mentionService->countMentionByDate($year, $month, $day, $hour));
    }
}
