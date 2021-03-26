<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'user.username.label',
                'constraints' => [
                    new NotBlank([
                        'message' => 'user.username.notvalid'
                    ]),
                    new Regex([
                        'pattern' => '/^[A-Za-z]\\w{3,19}$/',
                        'match' => true,
                        'message' => 'user.username.regex'
                    ])
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'user.firstname.label',
                'constraints' => [
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'user.password.max'
                    ]),
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'user.lastname.label',
                'constraints' => [
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'user.password.max'
                    ]),
                ]
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
