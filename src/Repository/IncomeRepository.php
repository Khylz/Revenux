<?php

namespace App\Repository;

use App\Entity\Income;
use App\Entity\Period;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Income>
 *
 * @method Income|null find($id, $lockMode = null, $lockVersion = null)
 * @method Income|null findOneBy(array $criteria, array 'orderBy' = null)
 * @method Income[]    findAll()
 * @method Income[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IncomeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Income::class);
    }

    public function save(Income $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Income $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Income[] Returns an array of Income objects for a given user
     */
    public function findByUserIncomes(User $user): array
    {
        return $this->createQueryBuilder('i')
            ->join('i.period', 'p')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
            ->orderBy('i.incomeDate', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getTotalIncomeByPeriodAndUser(Period $period, User $user): float
    {
        return (float) $this->createQueryBuilder('i')
            ->select('SUM(i.amount)')
            ->where('i.period = :period')
            ->andWhere('i.period IN (SELECT p2.id FROM App\\Entity\\Period p2 WHERE p2.user = :user)')
            ->setParameter('period', $period)
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTotalAmount(): float
    {
        return $this->createQueryBuilder('i')
            ->select('SUM(i.amount) as total')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    public function getCategoriesWithTotal(): array
    {
        return $this->createQueryBuilder('i')
            ->select('c.name, SUM(i.amount) as total')
            ->join('i.incomeCategory', 'c')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();
    }

    public function getTotalByUserAndPeriod($user, $start, $end)
    {
        return $this->createQueryBuilder('i')
            ->select('SUM(i.amount)')
            ->where('i.user = :user')
            ->andWhere('i.incomeDate >= :start')
            ->andWhere('i.incomeDate <= :end')
            ->setParameter('user', $user)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    public function getTotalByUser($user)
    {
        return $this->createQueryBuilder('i')
            ->select('SUM(i.amount)')
            ->where('i.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }
} 