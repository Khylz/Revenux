<?php

namespace App\Form;

use App\Entity\Expense;
use App\Entity\ExpenseCategory;
use App\Entity\Period;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use App\Repository\PeriodRepository;
use App\Repository\ExpenseCategoryRepository;

class ExpenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        $builder
            ->add('period', EntityType::class, [
                'class' => Period::class,
                'choice_label' => 'periodName',
                'placeholder' => '-- Choisir une période --',
                'query_builder' => function (PeriodRepository $pr) use ($user) {
                    return $pr->createQueryBuilder('p')
                        ->where('p.user = :user')
                        ->setParameter('user', $user)
                        ->orderBy('p.startDate', 'DESC');
                },
                'constraints' => [
                    new NotNull(),
                ],
            ])
            ->add('expenseCategory', EntityType::class, [
                'class' => ExpenseCategory::class,
                'choice_label' => 'categoryName',
                'placeholder' => '-- Choisir une catégorie --',
                'query_builder' => function (ExpenseCategoryRepository $ecr) use ($user) {
                    return $ecr->createQueryBuilder('c')
                        ->where('c.user = :user')
                        ->setParameter('user', $user)
                        ->orderBy('c.categoryName', 'ASC');
                },
                'constraints' => [
                    new NotNull(),
                ],
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Length(['max' => 255]),
                ],
            ])
            ->add('amount', MoneyType::class, [
                'currency' => 'EUR',
                'constraints' => [
                    new NotBlank(),
                    new PositiveOrZero(),
                ],
            ])
            ->add('expenseDate', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Expense::class,
            'user' => null,
        ]);
    }
}
