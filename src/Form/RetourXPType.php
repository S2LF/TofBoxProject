<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RetourXPType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('juger', ChoiceType::class,[
                'label' => 'Globalement, comment jugeriez-vous votre découverte et visite du site ?',
                'choices' => [
                    'Excellente' => 'Excellente',
                    'Sympa' => 'Sympa',
                    'Mauvaise' => 'Mauvaise',
                    'Pitoyable' => 'Pitoyable'
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('fluide', ChoiceType::class,[
                'label' => 'Votre expérience a-t-elle été fluide ?',
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('boutons', ChoiceType::class,[
                'label' => 'Est-ce que les boutons sont bien situés (pour vous) ?',
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('xp_propositions', TextareaType::class, [
                'label' => 'Auriez-vous des propositions ?' ])



            ->add('photo', ChoiceType::class,[
                'label' => 'La manière de voir les photos vous parait-elle bien ?',
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('upload', ChoiceType::class,[
                'label' => "La manière d'ajouter une photo vous parait-elle bien ?",
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('like', ChoiceType::class,[
                'label' => 'Le système de like vous paraît-il bien ?',
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('photo_propositions', TextareaType::class, [
                'label' => 'Auriez-vous des propositions ?' ])
                

            ->add('user', ChoiceType::class,[
                'label' => 'Votre espace utilisateur est-il ergonomique ?',
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('follow', ChoiceType::class,[
                'label' => 'Le système de follow vous paraît-il bien ?',
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('user_propositions', TextareaType::class, [
                'label' => 'Auriez-vous des propositions ?' ])


            ->add('design', ChoiceType::class,[
                'label' => 'Le site est-il agréable à regarder ?',
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('valeur', ChoiceType::class,[
                'label' => 'Le site met-il les photos en valeur ?',
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('design_propositions', TextareaType::class, [
                'label' => 'Auriez-vous des propositions ?' ])

            ->add('rgpd', ChoiceType::class,[
                'label' => "",
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('claire', ChoiceType::class,[
                'label' => 'Si oui, est-ce que les termes vous paraissent claire ?',
                'choices' => [
                    'Oui' => 'Oui',
                    'Non' => 'Non',
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('rgpd_propositions', TextareaType::class, [
                'label' => 'Auriez-vous des propositions ?' ])

            ->add('sug_propositions', TextareaType::class, [
                'label' => 'Auriez-vous des propositions pour améliorer le site ?' ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
