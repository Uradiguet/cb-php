<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Form\BudgetType;
use App\Repository\BudgetRepository;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/budgets')]
class BudgetController extends AbstractController
{
    #[Route('/', name: 'app_budget_index')]
    public function index(BudgetRepository $budgetRepository, TransactionRepository $transactionRepository): Response
    {
        $user = $this->getUser();
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');

        $budgets = $budgetRepository->findByUserAndPeriod($user, $currentYear, $currentMonth);
        $monthlyStats = $transactionRepository->getMonthlyStats($user, $currentYear, $currentMonth);

        // Calculer l'utilisation de chaque budget
        $budgetAnalysis = [];
        foreach ($budgets as $budget) {
            $spent = 0;
            if ($budget->getCategory()) {
                // Calculer les dépenses pour cette catégorie spécifique
                foreach ($monthlyStats['transactions'] as $transaction) {
                    if ($transaction->getType() === 'expense' &&
                        $transaction->getCategory() === $budget->getCategory()) {
                        $spent += floatval($transaction->getAmount());
                    }
                }
            } else {
                // Si pas de catégorie, utiliser toutes les dépenses
                $spent = $monthlyStats['expenses'];
            }

            $budgetAnalysis[] = [
                'budget' => $budget,
                'spent' => $spent,
                'remaining' => floatval($budget->getAmount()) - $spent,
                'percentage' => floatval($budget->getAmount()) > 0 ?
                    ($spent / floatval($budget->getAmount())) * 100 : 0
            ];
        }

        return $this->render('budget/index.html.twig', [
            'budget_analysis' => $budgetAnalysis,
            'current_month' => $currentMonth,
            'current_year' => $currentYear,
        ]);
    }

    #[Route('/new', name: 'app_budget_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $budget = new Budget();
        $budget->setUser($this->getUser());

        $form = $this->createForm(BudgetType::class, $budget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($budget);
            $em->flush();

            $this->addFlash('success', 'Budget créé avec succès!');
            return $this->redirectToRoute('app_budget_index');
        }

        return $this->render('budget/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_budget_edit')]
    public function edit(Request $request, Budget $budget, EntityManagerInterface $em): Response
    {
        if ($budget->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(BudgetType::class, $budget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Budget modifié avec succès!');
            return $this->redirectToRoute('app_budget_index');
        }

        return $this->render('budget/edit.html.twig', [
            'form' => $form->createView(),
            'budget' => $budget,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_budget_delete', methods: ['POST'])]
    public function delete(Request $request, Budget $budget, EntityManagerInterface $em): Response
    {
        if ($budget->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $budget->getId(), $request->request->get('_token'))) {
            $em->remove($budget);
            $em->flush();
            $this->addFlash('success', 'Budget supprimé avec succès!');
        }

        return $this->redirectToRoute('app_budget_index');
    }
}