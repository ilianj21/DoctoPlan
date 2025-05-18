<?php

namespace App\Form;

use App\Entity\TimeSlot;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimeSlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $b, array $o): void
    {
        $today = new \DateTimeImmutable('today');

        $b
            ->add('startAt', DateType::class, [
                'widget'    => 'single_text',
                'html5'     => true,
                'attr'      => ['min' => $today->format('Y-m-d')],
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
            ])
            ->add('endAtTime', TimeType::class, [
                'mapped'    => false,
                'widget'    => 'choice',
                'hours'     => [8,9,10,11,12,14,15,16,17],
                'minutes'   => [0,30],
                'label'     => 'Heure de fin',
            ])
            ->add('doctor', EntityType::class, [
                'class'        => User::class,
                'choice_label' => 'name',
                'placeholder'  => 'Sélectionnez un médecin',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $r): void
    {
        $r->setDefaults([
            'data_class' => TimeSlot::class,
        ]);
    }
}
