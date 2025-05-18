<?php

namespace App\Form;

use App\Entity\Appointment;
use App\Entity\TimeSlot;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppointmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status')
            ->add('patient', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('doctor', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('timeSlot', EntityType::class, [
                'class' => TimeSlot::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
        ]);
    }
}
