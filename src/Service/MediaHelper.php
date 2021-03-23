<?php

namespace App\Service;

use App\Entity\Trick;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validation;

class MediaHelper
{
    private $validator;
    private $constraintImage;
    private $entityManager;
    
    public function __construct(EntityManager $entityManager)
    {
        $this->validator = Validation::createValidator();
        $this->constraintImage = new File([
            'maxSize' => '2048k',
            'mimeTypes' => [
                    'image/png',
                    'image/bmp',
                    'image/gif',
                    'image/jpeg',
                    'image/webp',
            ],
        ]);
        $this->entityManager = $entityManager;
    }

    public function isUploadedImageValid(UploadedFile $uploadedFile) : bool
    {
        $errors = $this->validator->validate($uploadedFile, $this->constraintImage);
        if(count($errors) > 0)
            return false;
        return true;
    }

    public function trickUploadedCollectionAnalyzer(Trick $trick, array $uploadedCollection)
    {
        
    }
}