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
    private UserRepository     $userRepo;

    public function __construct(TimeSlotRepository $timeSlotRepo, UserRepository $userRepo)
    {
        $this->timeSlotRepo = $timeSlotRepo;
        $this->userRepo     = $userRepo;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // --- Patient uniquement ROLE_USER
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

            // --- Médecin uniquement ROLE_DOCTOR + auto‐submit
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

            // --- TimeSlot vide au chargement
            ->add('timeSlot', EntityType::class, [
                'class'       => TimeSlot::class,
                'choices'     => [],   // on remplit via PRE_SUBMIT
                'placeholder' => 'Choisissez un médecin d\'abord',
            ])

            ->add('status')
        ;

        // fonction utilitaire pour (re)peupler timeSlot
        $populateSlots = function(FormInterface $form, ?User $doctor) {
            $slots = $doctor
                ? $this->timeSlotRepo->findAvailableSlotsForDoctor($doctor)
                : [];
            $form->add('timeSlot', EntityType::class, [
                'class'        => TimeSlot::class,
                'choices'      => $slots,
                'choice_label' => fn(TimeSlot $t) =>
                    $t->getStartAt()->format('d/m/Y H:i')
                  . ' – ' . $t->getEndAt()->format('H:i'),
                'placeholder'  => 'Sélectionnez un créneau',
            ]);
        };

        // 1) Replis au premier GET (édition)
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $e) use($populateSlots) {
            $appointment = $e->getData();
            $populateSlots($e->getForm(), $appointment->getDoctor());
        });

        // 2) Replis après POST (submit intermédiaire médecin)
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $e) use($populateSlots) {
            $data = $e->getData();
            $doctor = $data['doctor'] 
                ? $this->userRepo->find($data['doctor']) 
                : null;
            $populateSlots($e->getForm(), $doctor);
        });

        // 3) (optionnel) Replis aussi après POST_SUBMIT sur doctor, si vous gériez en AJAX
        $builder->get('doctor')->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $e) use($populateSlots) {
                $doctor = $e->getForm()->getData();
                $populateSlots($e->getForm()->getParent(), $doctor);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
            'method'     => 'POST',
        ]);
    }
}
