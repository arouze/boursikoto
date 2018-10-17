<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MentionRepository")
 */
class Mention
{
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
    private $user_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_followers;

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
