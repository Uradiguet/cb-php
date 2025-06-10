<?php

namespace App\Form;

use App\Entity\Budget;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class BudgetType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du budget',
                'attr' => ['class' => 'input input-bordered w-full']
            ])
            ->add('amount', MoneyType::class, [
                'label' => 'Montant',
                'currency' => 'EUR',
                'attr' => ['class' => 'input input-bordered w-full']
            ])
            ->add('month', ChoiceType::class, [
                'label' => 'Mois',
                'choices' => [
                    'Janvier' => 1, 'Février' => 2, 'Mars' => 3, 'Avril' => 4,
                    'Mai' => 5, 'Juin' => 6, 'Juillet' => 7, 'Août' => 8,
                    'Septembre' => 9, 'Octobre' => 10, 'Novembre' => 11, 'Décembre' => 12
                ],
                'data' => $currentMonth,
                'attr' => ['class' => 'select select-bordered w-full']
            ])
            ->add('year', ChoiceType::class, [
                'label' => 'Année',
                'choices' => array_combine(
                    range($currentYear - 1, $currentYear + 2),
                    range($currentYear - 1, $currentYear + 2)
                ),
                'data' => $currentYear,
                'attr' => ['class' => 'select select-bordered w-full']
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'required' => false,
                'placeholder' => 'Toutes les catégories',
                'attr' => ['class' => 'select select-bordered w-full'],
                'query_builder' => function(CategoryRepository $repository) use ($user) {
                    return $repository->createQueryBuilder('c')
                        ->where('c.user = :user')
                        ->setParameter('user', $user)
                        ->orderBy('c.name', 'ASC');
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Budget::class,
        ]);
    }
}