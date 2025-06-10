<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transactions')]
class TransactionController extends AbstractController
{
    #[Route('/', name: 'app_transaction_index')]
    public function index(TransactionRepository $transactionRepository): Response
    {
        $user = $this->getUser();

        $transactions = $transactionRepository->createQueryBuilder('t')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->orderBy('t.date', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('transaction/index.html.twig', [
            'transactions' => $transactions,
        ]);
    }

    #[Route('/new', name: 'app_transaction_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $transaction = new Transaction();
        $transaction->setUser($this->getUser());

        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($transaction);
            $em->flush();

            $this->addFlash('success', 'Transaction ajoutée avec succès!');
            return $this->redirectToRoute('app_transaction_index');
        }

        return $this->render('transaction/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transaction_edit')]
    public function edit(Request $request, Transaction $transaction, EntityManagerInterface $em): Response
    {
        if ($transaction->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Transaction modifiée avec succès!');
            return $this->redirectToRoute('app_transaction_index');
        }

        return $this->render('transaction/edit.html.twig', [
            'form' => $form->createView(),
            'transaction' => $transaction,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_transaction_delete', methods: ['POST'])]
    public function delete(Request $request, Transaction $transaction, EntityManagerInterface $em): Response
    {
        if ($transaction->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $transaction->getId(), $request->request->get('_token'))) {
            $em->remove($transaction);
            $em->flush();
            $this->addFlash('success', 'Transaction supprimée avec succès!');
        }

        return $this->redirectToRoute('app_transaction_index');
    }
}