<?php

namespace App\Repository;

use App\Entity\Expense;
use App\Entity\Period;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Expense>
 *
 * @method Expense|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expense|null findOneBy(array $criteria, array $orderBy = null)
 * @method Expense[]    findAll()
 * @method Expense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    public function save(Expense $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Expense $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Expense[] Returns an array of Expense objects for a given user
     */
    public function findByUserExpenses(User $user): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.period', 'p')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
            ->orderBy('e.expenseDate', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getTotalExpenseByPeriodAndUser(Period $period, User $user): float
    {
        return (float) $this->createQueryBuilder('e')
            ->select('SUM(e.amount)')
            ->where('e.period = :period')
            ->andWhere('e.period IN (SELECT p2.id FROM App\\Entity\\Period p2 WHERE p2.user = :user)')
            ->setParameter('period', $period)
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getTotalAmount(): float
    {
        return $this->createQueryBuilder('e')
            ->select('SUM(e.amount) as total')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    public function getCategoriesWithTotal(): array
    {
        return $this->createQueryBuilder('e')
            ->select('c.name, SUM(e.amount) as total')
            ->join('e.expenseCategory', 'c')
            ->groupBy('c.id')
            ->getQuery()
            ->getResult();
    }
} 