<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function banUserById($id) {
        $user = new User();
        $user->setTwtId($id);
        $user->setBannedDate(new \DateTime());
        $user->setIsBanned(1);

        dump($user);

        try {
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}
