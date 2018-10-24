<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User
{

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
    }

    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $twt_id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isBanned;

    /**
     * @ORM\Column(type="datetime")
     */
    private $bannedDate;

    /**
     * @return mixed
     */
    public function getBannedDate()
    {
        return $this->bannedDate;
    }

    /**
     * @param mixed $bannedDate
     */
    public function setBannedDate($bannedDate): void
    {
        $this->bannedDate = $bannedDate;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTwtId()
    {
        return $this->twt_id;
    }

    /**
     * @param mixed $twt_id
     */
    public function setTwtId($twt_id): void
    {
        $this->twt_id = $twt_id;
    }

    /**
     * @return mixed
     */
    public function isBanned()
    {
        return $this->isBanned;
    }

    /**
     * @param mixed $isBanned
     */
    public function setIsBanned($isBanned): void
    {
        $this->isBanned = $isBanned;
    }
}
