<?php

namespace App\Controller;

use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(TransactionRepository $transactionRepository): Response
    {
        $user = $this->getUser();

        // Statistiques mensuelles
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');

        $monthlyStats = $transactionRepository->getMonthlyStats($user, $currentYear, $currentMonth);

        // Dernières transactions
        $recentTransactions = $transactionRepository->findRecentByUser($user, 5);

        return $this->render('dashboard/index.html.twig', [
            'monthly_income' => $monthlyStats['income'],
            'monthly_expenses' => $monthlyStats['expenses'],
            'monthly_balance' => $monthlyStats['balance'],
            'recent_transactions' => $recentTransactions,
            'total_balance' => $user->getTotalBalance(),
        ]);
    }
}