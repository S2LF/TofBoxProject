<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ChangeMailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('password', PasswordType::class,[
            'label' => 'Mot de passe actuel :',
            'mapped' => false,
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir votre mot de passe actuel',
                ]),
                new UserPassword([
                    'message' => 'Le mot de passe est faux'
                ])
            ]
        ])
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'label'=> ' ',
                'invalid_message' => 'Les emails doivent correspondre',
                'first_options' => ['label' => 'Nouvel email :'],
                'second_options' => ['label' => 'Confirmer le nouvel email :'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuille saisir un email',
                    ]),
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
