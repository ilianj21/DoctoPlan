<?php

namespace App\Form;

use App\Entity\Appointment;
use App\Entity\TimeSlot;
use App\Entity\User;
use App\Repository\TimeSlotRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppointmentType extends AbstractType
{
    private TimeSlotRepository $timeSlotRepo;
    private UserRepository     $userRepo;

    public function __construct(TimeSlotRepository $timeSlotRepo, UserRepository $userRepo)
    {
        $this->timeSlotRepo = $timeSlotRepo;
        $this->userRepo     = $userRepo;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('patient', EntityType::class, [
                'class'         => User::class,
                'choice_label'  => 'name',
                'placeholder'   => 'Sélectionnez un patient',
                'query_builder' => fn(UserRepository $r) =>
                    $r->createQueryBuilder('u')
                      ->andWhere('u.roles LIKE :role')
                      ->setParameter('role', '%ROLE_USER%')
                      ->orderBy('u.name', 'ASC'),
            ])
            ->add('doctor', EntityType::class, [
                'class'         => User::class,
                'choice_label'  => 'name',
                'placeholder'   => 'Sélectionnez un médecin',
                'attr'          => ['onchange' => 'this.form.submit();'],
                'query_builder' => fn(UserRepository $r) =>
                    $r->createQueryBuilder('u')
                      ->andWhere('u.roles LIKE :role')
                      ->setParameter('role', '%ROLE_DOCTOR%')
                      ->orderBy('u.name', 'ASC'),
            ])
            ->add('timeSlot', EntityType::class, [
                'class'       => TimeSlot::class,
                'choices'     => $options['doctor'] 
                    ? $this->timeSlotRepo->findAvailableSlotsForDoctor($options['doctor']) 
                    : [],
                'choice_label'=> fn(TimeSlot $ts) =>
                    $ts->getStartAt()->format('d/m/Y H:i'),
                'placeholder' => 'Choisissez un créneau',
            ])
            ->add('status')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
            // transmettre le doctor sélectionné pour pré-charger les créneaux
            'doctor'     => null,
        ]);
    }
}
