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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppointmentType extends AbstractType
{
    private TimeSlotRepository $timeSlotRepo;
    private UserRepository $userRepo;

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
                'query_builder' => fn(UserRepository $repo) =>
                    $repo->createQueryBuilder('u')
                        ->andWhere('u.roles LIKE :role')
                        ->setParameter('role', '%ROLE_USER%')
                        ->orderBy('u.name', 'ASC'),
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
            ])
            ->add('timeSlot', EntityType::class, [
                'class'       => TimeSlot::class,
                'choices'     => [],
                'placeholder' => 'Choisissez un médecin d abord',
            ])
            ->add('status')
        ;

        $formModifier = function (FormInterface $form, ?User $doctor) {
            $slots = $doctor
                ? $this->timeSlotRepo->findAvailableSlotsForDoctor($doctor)
                : [];

            $form->add('timeSlot', EntityType::class, [
                'class'        => TimeSlot::class,
                'choices'      => $slots,
                'choice_label' => fn(TimeSlot $ts) =>
                    $ts->getStartAt()->format('d/m/Y H:i') . ' – ' . $ts->getEndAt()->format('H:i'),
                'placeholder'  => 'Sélectionnez un créneau',
            ]);
        };

        // Met à jour avant soumission complète (PRE_SUBMIT)
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($formModifier) {
            $data = $event->getData();
            $form = $event->getForm();
            $doctorId = $data['doctor'] ?? null;
            $doctor = $doctorId ? $this->userRepo->find($doctorId) : null;
            $formModifier($form, $doctor);
        });

        // Pré-remplit lors de l'édition (PRE_SET_DATA)
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formModifier) {
            $appointment = $event->getData();
            $form = $event->getForm();
            $formModifier($form, $appointment->getDoctor());
        });

        // Met à jour après choix du médecin (POST_SUBMIT)
        $builder->get('doctor')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $doctor = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $doctor);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
        ]);
    }
}
