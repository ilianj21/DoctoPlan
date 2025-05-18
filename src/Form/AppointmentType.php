<?php

// src/Form/AppointmentType.php
namespace App\Form;

use App\Entity\Appointment;
use App\Entity\TimeSlot;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppointmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('patient', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez un patient',
                'query_builder' => function(UserRepository $repo) {
                    return $repo->createQueryBuilder('u')
                        ->andWhere('u.roles LIKE :role')
                        ->setParameter('role', '%ROLE_USER%')
                        ->orderBy('u.name', 'ASC');
                },
            ])
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
            ->add('timeSlot', EntityType::class, [
                'class' => TimeSlot::class,
                'choice_label' => function(TimeSlot $ts) {
                    return $ts->getStartAt()->format('d/m/Y H:i')
                         . ' – ' . $ts->getEndAt()->format('H:i');
                },
                'placeholder' => 'Sélectionnez un créneau',
            ])
            ->add('status')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
        ]);
    }
}

