<?php

namespace App\Repository;

use App\Entity\IncomeCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IncomeCategory>
 *
 * @method IncomeCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method IncomeCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method IncomeCategory[]    findAll()
 * @method IncomeCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IncomeCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IncomeCategory::class);
    }

    public function save(IncomeCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(IncomeCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 