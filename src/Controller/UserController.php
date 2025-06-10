<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Operation;
use App\Form\UtilisateurFormType;
use App\Repository\UtilisateurRepository;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

#[Route("/user")]
class UserController extends AbstractController
{
    private UtilisateurRepository $utilisateurRepository;
    private CompteRepository $compteRepository;

    public function __construct(UtilisateurRepository $utilisateurRepository, CompteRepository $compteRepository)
    {
        $this->utilisateurRepository = $utilisateurRepository;
        $this->compteRepository = $compteRepository;
    }

    #[Route('/', name: 'app_user')]
    public function index(): Response
    {
        $users = $this->utilisateurRepository->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users,
            'menu' => [
                ['caption' => 'Utilisateurs', 'route' => 'app_user'],
                ['caption' => 'Ajouter un utilisateur', 'route' => 'user_add']
            ]
        ]);
    }

    #[Route("/add", name: 'user_add', methods: ['GET', 'POST'])]
    public function addUser(Request $request, EntityManagerInterface $em): Response
    {
        $user = new Utilisateur();
        $user->setLogin("BobLennon");

        // Create the form
        $form = $this->createForm(UtilisateurFormType::class, $user, [
            'action' => $this->generateUrl('user_add')
        ]);
        $form->add('save', SubmitType::class, ['label' => 'Valider']);

        // Handle the form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // If the form is valid, set the solde and save the user
            $user->setSolde(100.0); // Set solde explicitly if it's not set by the form
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_user');
        }

        return $this->render('user/add.html.twig', [
            'form' => $form->createView(),
            'menu' => [
                ['caption' => 'Utilisateurs', 'route' => 'app_user'],
                ['caption' => 'Ajouter un utilisateur', 'route' => 'user_add']
            ]
        ]);
    }

    #[Route('/add', methods: ['post'])]
    public function submitAddUser(EntityManagerInterface $entityManager, Request $request, ValidatorInterface $validator): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(UtilisateurFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', "L'utilisateur {$user->getLogin()} a été créé !");
        } else {
            $errors = $validator->validate($user);
            foreach ($errors as $error) {
                $this->addFlash('danger', $error->getMessage());
            }
        }
        return $this->redirectToRoute('app_user');
    }

    #[Route("/delete/{id}", name: "user_delete")]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $this->utilisateurRepository->find($id);
        if ($user !== null) {
            $entityManager->remove($user);
            $entityManager->flush();
        }
        return $this->redirectToRoute("app_user");
    }

    #[Route('/compte/{id}', name: 'compte_view')]
    public function viewCompte(int $id): Response
    {
        $compte = $this->utilisateurRepository->find($id);

        return $this->render('user/view.html.twig', [
            'compte' => $compte,
        ]);
    }


    #[Route('/compte/{id}/operation/add', name: 'operation_add')]
    public function addOperation(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Trouver l'utilisateur au lieu du compte
        $utilisateur = $this->utilisateurRepository->find($id);

        $operation = new Operation();
        $operation->setUtilisateur($utilisateur); // Associer à l'utilisateur

        $form = $this->createFormBuilder($operation)
            ->add('typeOperation', ChoiceType::class, [
                'choices' => [
                    'Débit' => false,
                    'Crédit' => true
                ],
                'label' => 'Type d\'opération'
            ])
            ->add('montant', MoneyType::class, [
                'label' => 'Montant',
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Le montant doit être supérieur ou égal à 0.',
                    ])
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'Ajouter opération'])
            ->add('libelle', TextType::class, ['label' => 'Libellé'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $operation = $form->getData();

            // Modifier le solde basé sur le type d'opération
            if ($operation->isTypeOperation() == 1) {
                $utilisateur->setSolde($utilisateur->getSolde() - $operation->getMontant());
                $this->addFlash('success', "Le débit a été effectuée avec succès pour l'utilisateur {$utilisateur->getLogin()}, il a maintenant un solde de {$utilisateur->getSolde()} €");
            } else {
                $utilisateur->setSolde($utilisateur->getSolde() + $operation->getMontant());
                $this->addFlash('success', "Le crédit a été effectuée avec succès pour l'utilisateur {$utilisateur->getLogin()}, il a maintenant un solde de {$utilisateur->getSolde()} €");
            }



            // Persister l'opération et l'utilisateur mis à jour
            $entityManager->persist($operation);
            $entityManager->persist($utilisateur); // Persister l'utilisateur également
            $entityManager->flush();


            return $this->redirectToRoute('app_user');
        }

        return $this->render('user/Operation/add.html.twig', [
            'form' => $form->createView(),
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/modify/{id}', name: 'user_modify', methods: ['GET', 'POST'])]
    public function modifyUser(
        Request $request,
        EntityManagerInterface $entityManager,
        Utilisateur $utilisateur
    ): Response {
        $form = $this->createForm(UtilisateurFormType::class, $utilisateur, [
            'action' => $this->generateUrl('user_modify', ['id' => $utilisateur->getId()])
        ]);

        $form->add('save', SubmitType::class, ['label' => 'Modifier']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', "L'utilisateur {$utilisateur->getLogin()} a été modifié !");
            return $this->redirectToRoute('app_user');
        }

        return $this->render('user/modify.html.twig', [
            'form' => $form->createView(),
            'menu' => [
                ['caption' => 'Utilisateurs', 'route' => 'app_user'],
                ['caption' => 'Ajouter un utilisateur', 'route' => 'user_add']
            ]
        ]);
    }


}
