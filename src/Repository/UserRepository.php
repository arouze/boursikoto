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
        $user->setTwtAccountId($id);
        $user->setBannedDate(new \DateTime());
        $user->setIsBanned(1);

        try {
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }


    public function getAllTwittersIds() {
        try {
            $result = $this->getEntityManager()->createQuery('SELECT GROUP_CONCAT(u.twt_account_id, \'\') from App\Entity\User u')->execute();
            return explode(',', $result[0][1]);
        } catch (\Exception $e) {
            throw new \Exception(`Can't get banned twitter accounts.`);
        }
    }
}
