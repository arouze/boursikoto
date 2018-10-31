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
use Symfony\Component\Routing\Generator\UrlGenerator;

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
        $currentDate = new \DateTime();

        $year = $request->get('year', $currentDate->format('Y'));
        $month = $request->get('month', $currentDate->format('m'));
        $day = $request->get('day', $currentDate->format('d'));
        $hour = $request->get('hour', null);
        $groupBy = $request->get('group_by', 'HOUR');

        $route =  'api-tweet-count';
        $routeParams = ['year' => $year];

        if ($month) {
            $route =  'api-tweet-count-month';
            $routeParams = [
                'year'  => $year,
                'month' => $month
            ];
        }

        if ($day) {
            $route =  'api-tweet-count-day';
            $routeParams = [
                'year'  => $year,
                'month' => $month,
                'day'   => $day
            ];
        }

        if ($hour) {
            $route =  'api-tweet-count-hour';
            $routeParams = [
                'year'  => $year,
                'month' => $month,
                'day'   => $day,
                'hour'  => $hour
            ];
        }

        return new Response($this->renderView('graph.html.twig', [
            'route'       => $route,
            'routeParams' => $routeParams,
            'group_by'    => $groupBy
        ]));

    }
}
