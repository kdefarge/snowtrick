<?php

namespace App\Form;

use App\Entity\Discussion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class DiscussionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('message', TextType::class, [
                'label' => 'Message',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nom d\'utilisateur.'
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Votre message doit comporter au moins {{ limit }} caractères',
                        'max' => 200,
                        'maxMessage' => 'Votre message doit comporter au plus {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Laisser un message',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Discussion::class,
        ]);
    }
}
