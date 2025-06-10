<?php

namespace App\Repository;

use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function findByUserAndDateRange(User $user, \DateTime $startDate, \DateTime $endDate): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->andWhere('t.date >= :startDate')
            ->andWhere('t.date <= :endDate')
            ->setParameter('user', $user)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('t.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findRecentByUser(User $user, int $limit = 10): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->orderBy('t.date', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getMonthlyStats(User $user, int $year, int $month): array
    {
        $startDate = new \DateTime("$year-$month-01");
        $endDate = clone $startDate;
        $endDate->modify('last day of this month')->setTime(23, 59, 59);

        $transactions = $this->findByUserAndDateRange($user, $startDate, $endDate);

        $income = 0;
        $expenses = 0;

        foreach ($transactions as $transaction) {
            if ($transaction->getType() === 'income') {
                $income += floatval($transaction->getAmount());
            } else {
                $expenses += floatval($transaction->getAmount());
            }
        }

        return [
            'income' => $income,
            'expenses' => $expenses,
            'balance' => $income - $expenses,
            'transactions' => $transactions
        ];
    }
}