<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'trick.label.name',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'trick.label.content',
            ])
            ->add('newcategory', TextType::class, [
                'label' => 'trick.label.newcategory',
                'mapped' => false,
                'required' => false
            ])
            ->add('category', EntityType::class, [
                'label' => 'trick.label.category',
                'class' => Category::class,
                'choice_label' => 'Name'
            ])
            ->add('pictures', CollectionType::class, [
                'label' => false,
                'entry_type' => FileType::class,
                'entry_options' => [
                    'label' => false,
                    'required' => false,
                ],
                'mapped' => false,
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('videolinks', CollectionType::class, [
                'label' => false,
                'entry_type' => UrlType::class,
                'entry_options' => [
                    'label' => false,
                    'required' => false,
                ],
                'mapped' => false,
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'form.save'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
