<?php

namespace App\Service;


use Abraham\TwitterOAuth\TwitterOAuth;
use App\Entity\Mention;
use App\Repository\MentionRepository;
use Doctrine\ORM\EntityManagerInterface;

class ParseService
{
    const TWITTER_DATE_FORMAT = 'D M d H:i:s P Y';
    const CURRENT_QUERY = 'apple';

    private $entityManager;
    private $mentionRepository;
    private $twitterAuthConnection;


    public function __construct(EntityManagerInterface $entityManager, MentionRepository $mentionRepository) {
        $this->entityManager = $entityManager;
        $this->mentionRepository = $mentionRepository;
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
        $result = $this->entityManager->createQuery('SELECT m.createdAt FROM App\Entity\Mention as m ORDER BY m.createdAt ASC')->setMaxResults(1)->getScalarResult();
        $oldestTweetDate = \DateTime::createFromFormat('Y-m-d H:i:s', $result[0]['createdAt']);

        $count = 0;

        while ($oldestTweetDate->diff($date)->invert === 1) {
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

        $result = $this->entityManager->createQuery('SELECT m.twt_id from App\Entity\Mention m')->getScalarResult();
        $ids = array_map('current', $result);

        $count = 0;

        foreach ($statuses->statuses as $status) {
            if (in_array($status->id_str, $ids)) {
                continue;
            }

            // Ignore RTs
            if (substr($status->text, 0, 2) === 'RT') {
                continue;
            }

            $mention = new Mention();
            $mention->setContentRaw($status->text);
            $mention->setTwtId($status->id_str);
            $mention->setCreatedAt(\DateTime::createFromFormat(self::TWITTER_DATE_FORMAT, $status->created_at));
            $mention->setUserName($status->user->screen_name);
            $mention->setUserId($status->user->id_str);
            $mention->setUserFollowers($status->user->followers_count);
            $mention->setSentimentScore(0);
            $mention->setScore(0);
            $mention->setSentimentMagnitude(0);
            $count++;
            $this->entityManager->persist($mention);
        }
        $this->entityManager->flush();

        return $count;
    }

    public function getTwitterStatus() {
        return json_decode(json_encode($this->twitterAuthConnection->get('application/rate_limit_status')->resources), true);
    }

}

