<?php

namespace App\Repository;

use App\Entity\PeriodSummary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PeriodSummary>
 *
 * @method PeriodSummary|null find($id, $lockMode = null, $lockVersion = null)
 * @method PeriodSummary|null findOneBy(array $criteria, array $orderBy = null)
 * @method PeriodSummary[]    findAll()
 * @method PeriodSummary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeriodSummaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PeriodSummary::class);
    }

    public function save(PeriodSummary $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PeriodSummary $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
} 