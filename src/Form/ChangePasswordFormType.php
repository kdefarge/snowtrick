<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'label' => 'user.newpassword.label',
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'user.password.notblank',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'user.password.min',
                            'max' => 4096,
                            'maxMessage' => 'user.password.max'
                        ]),
                    ],
                    'label' => 'user.newpassword.label',
                ],
                'second_options' => [
                    'label' => 'user.repeatpassword.label',
                ],
                'invalid_message' => 'user.repeatpassword.notmatch',
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
