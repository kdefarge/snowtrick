<?php

namespace App\Service;

use App\Entity\Media;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validation;

class UploadedManager
{
    private $validator;
    private $constraintImage;
    private $params;
    
    public function __construct(ParameterBagInterface $params)
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
        $this->params = $params;
    }

    public function getUploadDirectory(string $directory) : string
    {
        return $this->params->get('upload_directory').$directory;
    }

    public function imageValidToMediaEntity(UploadedFile $uploadedFile, string $directory) : ?Media
    {
        $errors = $this->validator->validate($uploadedFile, $this->constraintImage);
        if(count($errors) > 0)
            return null;
        
        $media = new Media();
        $newFilename = uniqid().'.'.$uploadedFile->guessExtension();
        try {
            $uploadedFile->move(
                $this->params->get('upload_directory').$directory,
                $newFilename
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        $media->setLink($directory.'/'.$newFilename);
        return $media;
    }

    public function deleteUploadedFile(Media $media) : void
    {
        $filesystem = new Filesystem();
        $filetodelete = $this->params->get('upload_directory').$media->getLink();
        if($filesystem->exists($filetodelete))
            $filesystem->remove($filetodelete);
    }
}
