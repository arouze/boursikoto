<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MentionRepository")
 */
class Mention
{
    const MENTION_BANNED_TERMS = ['Bitmex', 'Binance', 'Bitstamp', 'Bitexen'];

    const MENTION_STATUS_CREATED = 'CREATED';

    const MENTION_STATUS_ANALYSED = 'ANALYSED';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $twt_id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $content_raw;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $user_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $user_id; // @Todo Join on user table

    /**
     * @ORM\Column(type="integer")
     */
    private $user_followers;

    /**
     * @ORM\Column(type="float")
     */
    private $sentiment_score;

    /**
     * @ORM\Column(type="float")
     */
    private $sentiment_magnitude;

    /**
     * @ORM\Column(type="integer")
     */
    private $score;

    /** @ORM\Column(type="string", length=20, columnDefinition="ENUM('CREATED', 'ANALYSED')") */
    private $status = self::MENTION_STATUS_CREATED;

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getSentimentScore()
    {
        return $this->sentiment_score;
    }

    /**
     * @param mixed $sentiment_score
     */
    public function setSentimentScore($sentiment_score): void
    {
        $this->sentiment_score = $sentiment_score;
    }

    /**
     * @return mixed
     */
    public function getSentimentMagnitude()
    {
        return $this->sentiment_magnitude;
    }

    /**
     * @param mixed $sentiment_magnitude
     */
    public function setSentimentMagnitude($sentiment_magnitude): void
    {
        $this->sentiment_magnitude = $sentiment_magnitude;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param mixed $score
     */
    public function setScore($score): void
    {
        $this->score = $score;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTwtId(): ?string
    {
        return $this->twt_id;
    }

    public function setTwtId(string $twt_id): self
    {
        $this->twt_id = $twt_id;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getContentRaw(): ?string
    {
        return $this->content_raw;
    }

    public function setContentRaw(string $content_raw): self
    {
        $this->content_raw = $content_raw;

        return $this;
    }

    public function getUserName(): ?string
    {
        return $this->user_name;
    }

    public function setUserName(string $user_name): self
    {
        $this->user_name = $user_name;

        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->user_id;
    }

    public function setUserId(string $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getUserFollowers(): ?int
    {
        return $this->user_followers;
    }

    public function setUserFollowers(int $user_followers): self
    {
        $this->user_followers = $user_followers;

        return $this;
    }
}
