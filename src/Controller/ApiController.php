<?php

namespace App\Controller;


use App\Service\MentionService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends AbstractController
{
    private $mentionService;
    private $userService;

    public function __construct(MentionService $mentionService, UserService $userService)
    {
        $this->mentionService = $mentionService;
        $this->userService = $userService;
    }

    public function tweetCount($year, $month = null, $day = null, $hour = null, Request $request) {
        return new JsonResponse($this->mentionService->countMentionByDate($year, $month, $day, $hour, $request->get('groupBy')));
    }

    public function banUser($id) {
        $this->userService->banUserById($id);
        return new JsonResponse([]);
    }
}
