<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfilPictureFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('picture', FileType::class, [
                'label' => 'user.label.picture',
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'maxSizeMessage' => 'user.picture.maxsize',
                        'mimeTypes' => [
                            'image/svg',
                            'image/png',
                            'image/bmp',
                            'image/gif',
                            'image/jpeg',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'user.picture.mimetypes',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'form.save',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
