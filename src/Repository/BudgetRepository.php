<?php

namespace App\Repository;

use App\Entity\Budget;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Budget>
 */
class BudgetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Budget::class);
    }

    public function findByUserAndPeriod(User $user, int $year, int $month): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.user = :user')
            ->andWhere('b.year = :year')
            ->andWhere('b.month = :month')
            ->setParameter('user', $user)
            ->setParameter('year', $year)
            ->setParameter('month', $month)
            ->orderBy('b.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}