<?php

namespace App\Repository;

use App\Entity\Mention;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * @method Mention|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mention|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mention[]    findAll()
 * @method Mention[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MentionRepository extends ServiceEntityRepository
{

    const GROUP_BY_YEAR = 'YEAR';
    const GROUP_BY_MONTH = 'MONTH';
    const GROUP_BY_DAY = 'DAY';
    const GROUP_BY_HOUR = 'HOUR';

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Mention::class);
    }

    /**
     * @param Mention $mention
     * @throws \Doctrine\ORM\ORMException
     */
    public function save($mention) {
        try {
            $this->getEntityManager()->persist($mention);
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            throw new Exception($e);
        }
    }

    /**
     * @param string $year
     * @param string $groupBy
     * @return array
     */
    public function getMentionForYear($year, $groupBy = self::GROUP_BY_YEAR) {
        try {
            // @Todo Some refacto here
            $query = $this
                ->getEntityManager()
                ->getConnection()
                ->prepare('SELECT m.`created_at`, ' .
                    'COUNT(*) FROM mention as m ' .
                    'WHERE YEAR(m.`created_at`) = ' . $year .
                    ' ' . $this->getGroupBy($groupBy));

            $query->execute();

            return $query->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\Exception $e) {
            dump($e);
            exit;
        }
    }

    public function getMentionForMonth($year, $month, $groupBy = self::GROUP_BY_MONTH) {
        try {
            // @Todo Some refacto here
            $query = $this
                ->getEntityManager()
                ->getConnection()
                ->prepare('SELECT m.`created_at`, ' .
                    'COUNT(*) FROM mention as m '.
                    'WHERE MONTH(m.`created_at`) = ' . $month .
                    ' AND YEAR(m.`created_at`) =' . $year .
                    ' ' . $this->getGroupBy($groupBy));

            $query->execute();

            return $query->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\Exception $e) {
            dump($e);
            exit;
        }
    }

    public function getMentionForDay($year, $month, $day, $groupBy = self::GROUP_BY_HOUR) {
        try {
            // @Todo Some refacto here
            $query = $this
                ->getEntityManager()
                ->getConnection()
                ->prepare('SELECT m.`created_at`, ' .
                    'COUNT(*) FROM mention as m '.
                    'WHERE DAY(m.`created_at`) = ' . $day .
                    ' AND YEAR(m.`created_at`) =' . $year .
                    ' AND MONTH(m.`created_at`) =' . $month .
                    ' ' . $this->getGroupBy($groupBy));

            $query->execute();

            return $query->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\Exception $e) {
            dump($e);
            exit;
        }
    }

    public function getMentionForHour($year, $month, $day, $hour, $groupBy = self::GROUP_BY_HOUR) {
        try {
            // @Todo Some refacto here
            $query = $this
                ->getEntityManager()
                ->getConnection()
                ->prepare('SELECT m.`created_at`, ' .
                    'COUNT(*) FROM mention as m '.
                    'WHERE HOUR(m.`created_at`) = ' . $hour .
                    'AND DAY(m.`created_at`) =' . $day .
                    'AND YEAR(m.`created_at`) =' . $year .
                    'AND MONTH(m.`created_at`) =' . $month .
                    ' ' . $this->getGroupBy($groupBy));

            $query->execute();

            return $query->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\Exception $e) {
            dump($e);
            exit;
        }
    }

    /**
     * @param string $groupBy
     * @return string
     */
    private function getGroupBy($groupBy) {

        switch ($groupBy) {
            case self::GROUP_BY_HOUR:
                return 'GROUP BY HOUR(m.`created_at`)';
                break;
            case self::GROUP_BY_DAY:
                return 'GROUP BY DAY(m.`created_at`)';
                break;
            case self::GROUP_BY_MONTH:
                return 'GROUP BY MONTH(m.`created_at`)';
                break;
            default:
                return 'GROUP BY YEAR(m.`created_at`)';
        }
    }
}
