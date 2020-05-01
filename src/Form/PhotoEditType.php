<?php

namespace App\Form;

use App\Entity\Photo;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PhotoEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la photo*'
            ])

            ->add('description', TextareaType::class, [
                'label' => 'Description de la photo:' ])
            
            ->add('categories', EntityType::class, [
                'label' => 'Catégorie(s):',
                'class' => Category::class,
                'choice_label' => 'intitule',
                'expanded' => true,
                'multiple' => true,
                'attr' => [ 'class' => 'category_form' ]
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Photo::class,
        ]);
    }
}
