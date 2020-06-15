<?php

namespace App\Form;

use App\Entity\Photo;
use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class PhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre*:'
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo (JPG/PNG/GIF, max 1Mo)*',

                // Unmapped because not associated to any entity property
                'mapped' => false,
                
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => true,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif'
                        ],
                        'mimeTypesMessage' => 'Veuillez respecter les restrictions de taille et de format',
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de la photo:',
                'required' => false
])
 
            ->add('categories', EntityType::class, [
                'label' => 'Catégorie(s):',
                'class' => Category::class,
                'choice_label' => 'intitule',
                'expanded' => true,
                'multiple' => true,
                'attr' => [ 'required' => 'required' ],
                'choice_attr' => function(){
                    return [ 'class' => 'checkbox' ];
                } 
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
