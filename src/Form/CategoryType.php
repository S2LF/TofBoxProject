<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('intitule', TextType::class, [
                'label' => 'intitulé',
                'attr' => [
                    'placeholder' => 'Créer une catégorie'
                ]


            ])
                        // ->add('categories', EntityType::class, [
            //     'class' => Category::class,
            //     'choice_label' => 'intitule',
            //     'expanded' => true,
            //     'multiple' => true,
            //     'allow_add' => true,
            //     'allow_delete' =>true,
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
