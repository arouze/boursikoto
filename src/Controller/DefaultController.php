<?php
namespace App\Controller;

use App\Service\GoogleService;
use App\Service\MentionService;
use App\Service\ParseService;
use App\Service\UserService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// @Todo refactor Controller name
class DefaultController extends AbstractController
{
    private $parseService;

    private $googleService;
    private $mentionService;
    private $userService;

    public function __construct(
        ParseService $parseService,
        GoogleService $googleService,
        MentionService $mentionService,
        UserService $userService
    )
    {
        $this->parseService = $parseService;
        $this->googleService = $googleService;
        $this->mentionService = $mentionService;
        $this->userService = $userService;
    }

    public function lastTweet() {
        return new JsonResponse(['count' => $this->parseService->getLastTweets()]);
    }

    public function historicalTweets() {
        return new JsonResponse(['count' => $this->parseService->getHistoricalTweets()]);
    }

    public function status() {

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->connect();

        return new Response($this->renderView('status.html.twig',
            [
                'bannedUsers' => $this->userService->getBannedUserCount(),
                'twitterStatuses' => $this->parseService->getTwitterStatus(),
                'mentionsCount' => $this->mentionService->count()
            ]));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function analysis(Request $request) {

        $result = '';

        if ($request->isMethod(Request::METHOD_POST)) {
            $result = $this->googleService->sentimentAnalysis($request->get('content'));
        }

        return new Response($this->renderView('analysis.html.twig', [
            'result' => $result,
            'content' => $request->get('content')
        ]));
    }

    public function analyseMention($mentionId) {
        try {
            $this->mentionService->analyse($mentionId);
            return new Response(sprintf("Mention : %s updated", $mentionId));
        } catch(\Exception $e) {
            return new Response(sprintf("An error occured during mention update %s", $e->getMessage()));
        }
    }

    public function graph(Request $request) {
        return new Response($this->renderView('graph.html.twig'));
    }
}
