<?php

namespace App\Service;

use App\Entity\Media;
use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validation;

class MediaHelper
{
    private $validator;
    private $constraintImage;
    private $entityManager;
    private $session;
    private $params;
    
    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session, ParameterBagInterface $params)
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
        $this->session = $session;
        $this->params = $params;
    }

    public function isUploadedImageValid(UploadedFile $uploadedFile) : bool
    {
        $errors = $this->validator->validate($uploadedFile, $this->constraintImage);
        if(count($errors) > 0)
            return false;
        return true;
    }

    public function uploadedImageToMediaEntity(UploadedFile $uploadedFile) : Media
    {
        $media = new Media();
        $newFilename = uniqid().'.'.$uploadedFile->guessExtension();
        try {
            $uploadedFile->move(
                $this->params->get('trick_upload_directory'),
                $newFilename
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }
        $media->setLink($this->params->get('trick_upload_asset').$newFilename);
        return $media;
    }
}
