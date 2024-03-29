<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'user.label.email',
                'disabled' => true,
            ])
            ->add('username', TextType::class, [
                'label' => 'user.label.username',
                'disabled' => true,
            ])
            ->add('firstname', TextType::class, [
                'label' => 'user.label.firstname',
                'required' => false,
            ])
            ->add('lastname', TextType::class, [
                'label' => 'user.label.lastname',
                'required' => false,
            ])
            ->add('edit', SubmitType::class, [
                'label' => 'form.edit'
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
