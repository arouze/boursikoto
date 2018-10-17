<?php
namespace App\Controller;

use App\Service\GoogleService;
use App\Service\ParseService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    private $parseService;

    private $googleService;

    public function __construct(
        ParseService $parseService,
        GoogleService $googleService
    )
    {
        $this->parseService = $parseService;
        $this->googleService = $googleService;
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
                'mysqlStatus' => $em->getConnection()->isConnected(),
                'twitterStatuses' => $this->parseService->getTwitterStatus()
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
}
