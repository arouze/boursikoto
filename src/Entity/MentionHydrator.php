<?php

namespace App\Entity;


class MentionHydrator
{
    const TWITTER_DATE_FORMAT = 'D M d H:i:s P Y';

    /**
     * @param object $status
     * @return Mention
     */
    public function hydrate($status): Mention {
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

        return $mention;
    }
}
