<?php

/**
 * Managing uploaded files service
 *
 * @author     Kevin DEFARGE <kdefarge@gmail.com>
 */

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UploadedManager
{
    private $constraintImage;
    private $params;
    private $validator;

    public function __construct(ParameterBagInterface $params, ValidatorInterface $validator)
    {
        $this->constraintImage = new File([
            'maxSize' => '2048k',
            'mimeTypes' => [
                'image/svg',
                'image/png',
                'image/bmp',
                'image/gif',
                'image/jpeg',
                'image/webp',
            ],
        ]);
        $this->params = $params;
        $this->validator = $validator;
    }

    /**
     * Checks if the uploaded file is a valid image
     *
     * @param UploadedFile $uploadedFile The uploaded file that we want to test
     * 
     * @return bool returns true this is the valid uploaded file
     */
    public function validateImage(UploadedFile $uploadedFile): bool
    {
        $errors = $this->validator->validate($uploadedFile, $this->constraintImage);
        if (count($errors) > 0)
            return false;
        return true;
    }

    /**
     * Move and return the location of the uploaded file
     *
     * @param UploadedFile $uploadedFile the uploaded file that we want to process
     * @param string $directory the folder where we put the uploaded file
     * 
     * @return string returns the location of the file
     */
    public function moveAndGetLink(UploadedFile $uploadedFile, string $directory): string
    {
        $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();

        try {
            $uploadedFile->move(
                $this->params->get('upload_directory') . $directory,
                $newFilename
            );
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $directory . '/' . $newFilename;
    }

    /**
     * Removes the file
     *
     * @param string The file you want to delete
     * 
     * @return void
     */
    public function deleteUploadedFile(string $link): void
    {
        $filesystem = new Filesystem();
        $filetodelete = $this->params->get('upload_directory') . $link;
        if ($filesystem->exists($filetodelete))
            $filesystem->remove($filetodelete);
    }
}
