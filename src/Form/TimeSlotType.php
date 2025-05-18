<?php
// src/Form/TimeSlotType.php
namespace App\Form;

use App\Entity\TimeSlot;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimeSlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startAt')
            ->add('endAt')
            ->add('doctor', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez un médecin',
                'query_builder' => function(UserRepository $repo) {
                    return $repo->createQueryBuilder('u')
                        ->andWhere('u.roles LIKE :role')
                        ->setParameter('role', '%ROLE_DOCTOR%')
                        ->orderBy('u.name', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TimeSlot::class,
        ]);
    }
}
