<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Transaction;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class TransactionType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();

        $builder
            ->add('description', TextType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'input input-bordered w-full']
            ])
            ->add('amount', MoneyType::class, [
                'label' => 'Montant',
                'currency' => 'EUR',
                'attr' => ['class' => 'input input-bordered w-full']
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Revenu' => 'income',
                    'Dépense' => 'expense'
                ],
                'attr' => ['class' => 'select select-bordered w-full']
            ])
            ->add('date', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'attr' => ['class' => 'input input-bordered w-full']
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'required' => false,
                'placeholder' => 'Choisir une catégorie...',
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
            'data_class' => Transaction::class,
        ]);
    }
}