<?php
// src/Form/UserType.php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType; 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //Nom
            ->add('name', TextType::class, [
            'label' => 'Nom complet',
            ])

            // Email
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
            ])
            
            // Mot de passe
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                // en prod, n’oubliez pas d’encoder ce password dans le controller
            ])

            // Rôles : cases à cocher, toujours renvoie un array
            ->add('roles', ChoiceType::class, [
                'label'    => 'Rôles',
                'choices'  => [
                    'Utilisateur'    => 'ROLE_USER',
                    'Médecin'        => 'ROLE_DOCTOR',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true,  // true = checkboxes, false = <select multiple>
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
