<?php

namespace App\Form;

use App\Entity\TimeSlot;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimeSlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $today = new \DateTimeImmutable('today');

        $builder
            ->add('startAt', DateType::class, [
                'widget'    => 'single_text',
                'html5'     => true,
                'attr'      => ['min' => $today->format('Y-m-d')],
                'label'     => 'Date de début',
            ])
            ->add('startAtTime', TimeType::class, [
                'mapped'    => false,
                'widget'    => 'choice',
                'hours'     => [8,9,10,11,12,14,15,16,17],
                'minutes'   => [0,30],
                'label'     => 'Heure de début',
            ])
            ->add('endAt', DateType::class, [
                'widget'    => 'single_text',
                'html5'     => true,
                'attr'      => ['min' => $today->format('Y-m-d')],
                'label'     => 'Date de fin',
            ])
            ->add('endAtTime', TimeType::class, [
                'mapped'    => false,
                'widget'    => 'choice',
                'hours'     => [8,9,10,11,12,14,15,16,17],
                'minutes'   => [0,30],
                'label'     => 'Heure de fin',
            ])
            ->add('doctor', EntityType::class, [
                'class'         => User::class,
                'choice_label'  => 'name',
                'placeholder'   => 'Sélectionnez un médecin',
                'query_builder' => fn(UserRepository $repo) =>
                    $repo->createQueryBuilder('u')
                         ->andWhere('u.roles LIKE :role')
                         ->setParameter('role', '%ROLE_DOCTOR%')
                         ->orderBy('u.name', 'ASC'),
                'label'         => 'Médecin',
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
