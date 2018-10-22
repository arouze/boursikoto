<?php

namespace App\Service;


use Abraham\TwitterOAuth\TwitterOAuth;
use App\Entity\Mention;
use App\Entity\MentionHydrator;
use App\Repository\MentionRepository;
use Doctrine\ORM\EntityManagerInterface;

class ParseService
{
    const CURRENT_QUERY = 'xrp';

    private $entityManager;
    private $mentionRepository;
    private $twitterAuthConnection;


    public function __construct(EntityManagerInterface $entityManager, MentionRepository $mentionRepository) {
        $this->entityManager = $entityManager;
        $this->mentionRepository = $mentionRepository;
        // @Todo refactor getenv set this in parameter bag
        $this->twitterAuthConnection = new TwitterOAuth(
            getenv('TWITTER_CONSUMER_KEY'),
            getenv('TWITTER_CONSUMER_SECRET'),
            getenv('TWITTER_ACCESS_TOKEN'),
            getenv('TWITTER_ACCESS_TOKEN_SECRET')
        );
    }

    public function getLastTweets():int {
        $statuses = $this->twitterAuthConnection->get("search/tweets", ["q" => self::CURRENT_QUERY, "result_type" => "recent", "count" => "100"]);
        return $this->insertNewTweets($statuses);
    }

    public function getHistoricalTweets() {
        $date = new \DateTime('2018-09-01');
        // @Todo Do this in repository
        $result = $this->entityManager->createQuery('SELECT m.createdAt FROM App\Entity\Mention as m ORDER BY m.createdAt ASC')->setMaxResults(1)->getScalarResult();
        $oldestTweetDate = \DateTime::createFromFormat('Y-m-d H:i:s', $result[0]['createdAt']);

        $count = 0;

        while ($oldestTweetDate->diff($date)->invert === 1) {
            // @Todo Do this in repository
            $result = $this->entityManager->createQuery('SELECT m.createdAt FROM App\Entity\Mention as m ORDER BY m.createdAt ASC')->setMaxResults(1)->getScalarResult();
            $oldestTweetDate = \DateTime::createFromFormat('Y-m-d H:i:s', $result[0]['createdAt']);

            $statuses = $this->twitterAuthConnection->get("search/tweets", [
                "q" => self::CURRENT_QUERY,
                "result_type" => "recent",
                "count" => "100",
                "until" => $oldestTweetDate->format('Y-m-d')
            ]);

            dump($statuses);
            exit;

            $count = $count + $this->insertNewTweets($statuses);
        }

        return $count;
    }

    private function insertNewTweets($statuses) {

        $count = 0;

        foreach ($statuses->statuses as $status) {
            if (in_array($status->id_str, array_map('current', $this->mentionRepository->getAllIds()))) {
                continue;
            }

            // Ignore RTs
            if (substr($status->text, 0, 2) === 'RT') {
                continue;
            }

            $mentionHydrator = new MentionHydrator();
            $this->mentionRepository->save($mentionHydrator->hydrate($status));
            $count++;
        }
        return $count;
    }

    public function getTwitterStatus() {
        return json_decode(json_encode($this->twitterAuthConnection->get('application/rate_limit_status')->resources), true);
    }

}

