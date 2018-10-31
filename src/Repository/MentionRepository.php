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
     * @return Mention[]
     */
    public function getAllMentionsWithoutSentimentScore() {
        $qb = $this->createQueryBuilder('m')
            ->andWhere('m.status = :status')
            ->setParameter('status', Mention::MENTION_STATUS_CREATED)
            ->setMaxResults(50)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery();

        return $qb->execute();
    }

    /**
     * @return array
     */
    public function getAllIds() {
        return $this->getEntityManager()->createQuery('SELECT m.twt_id from App\Entity\Mention m')->getScalarResult();
    }

    /**
     * @param integer $year
     * @param null|integer $month
     * @param null|integer $day
     * @param null|integer $hour
     * @param string $groupBy
     * @return array
     */
    public function getMentionForPeriod($year, $month = null, $day = null, $hour = null, $groupBy = self::GROUP_BY_YEAR) {
        try {
            $query = $this
                ->getEntityManager()
                ->getConnection()
                ->prepare(
                    $this->dateFormat($groupBy) .
                    $this->getPeriodRequest($year, $month, $day, $hour) .
                    ' ' . $this->getGroupBy($groupBy));

            $query->execute();

            return $query->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\Exception $e) {
            dump($e);
            exit;
        }
    }

    private function getPeriodRequest($year, $month = null, $day = null, $hour = null) {

        if (!isset($year)) {
            throw new Exception(`Can't search mention without Year`);
        }

        $request = 'COUNT(*) as value FROM mention as m ' .
            'WHERE YEAR(m.`created_at`) = ' . $year;

        if (isset($month)) {
            $request .= ' AND MONTH(m.`created_at`) = ' . $month;
        }

        if (isset($day)) {
            $request .= ' AND DAY(m.`created_at`) = ' . $day;
        }

        if (isset($hour)) {
            $request .= ' AND HOUR(m.`created_at`) = ' . $hour;
        }

        return $request;
    }

    private function dateFormat($groupBy) {
        $select = 'SELECT ';

        switch ($groupBy) {
            case self::GROUP_BY_HOUR:
                $select .= ' m.`created_at`';
                break;
            case self::GROUP_BY_DAY:
                $select .= 'DATE(m.`created_at`)';
                break;
            case self::GROUP_BY_MONTH:
                $select .= 'DATE(m.`created_at`)';
                break;
            default:
                $select .= 'DATE_FORMAT(m.`created_at`, \'%Y\')';
        }

        return $select . ' as date, ';
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
