<?php

namespace App\Form;

use App\Entity\Appointment;
use App\Entity\TimeSlot;
use App\Entity\User;
use App\Repository\TimeSlotRepository;
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

    public function __construct(TimeSlotRepository $timeSlotRepo)
    {
        $this->timeSlotRepo = $timeSlotRepo;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('patient', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez un patient',
            ])
            ->add('doctor', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez un médecin',
            ])
            // initial empty field, will be modified by event
            ->add('timeSlot', EntityType::class, [
                'class' => TimeSlot::class,
                'choices' => [],
                'placeholder' => 'Choisissez d\'abord un médecin',
            ])
            ->add('status')
        ;

        $formModifier = function (FormInterface $form, ?User $doctor) {
            $slots = $doctor
                ? $this->timeSlotRepo->findAvailableSlotsForDoctor($doctor)
                : [];
            $form->add('timeSlot', EntityType::class, [
                'class' => TimeSlot::class,
                'choices' => $slots,
                'choice_label' => function (TimeSlot $ts) {
                    return $ts->getStartAt()->format('d/m/Y H:i')
                         . ' – ' . $ts->getEndAt()->format('H:i');
                },
                'placeholder' => 'Sélectionnez un créneau',
            ]);
        };

        // Pre-populate on edit
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formModifier) {
            $data = $event->getData();
            $form = $event->getForm();
            $formModifier($form, $data->getDoctor());
        });

        // Update timeSlot choices when doctor changes
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
