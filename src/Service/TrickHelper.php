<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;

class TrickHelper
{
    private $entityManager;
    private $mediaHelper;
    
    public function __construct(EntityManagerInterface $entityManager, MediaHelper $mediaHelper)
    {
        $this->entityManager = $entityManager;
        $this->mediaHelper = $mediaHelper;
    }

    public function formToDatabase(Trick $trick, Form $form) : void //on peut pas utiliser un forminterface???
    {
        $newcategory = $form->get('newcategory')->getData();
        if('' !== $newcategory && null !== $newcategory) {
            $category = new Category();
            $category->setName($newcategory);
            $trick->setCategory($category);
            $this->entityManager->persist($category);
        }

        $uploadedFileCollection = $form->get('medias')->getData();
        foreach($uploadedFileCollection as $uploadedFile) {
            if($uploadedFile && $this->mediaHelper->isUploadedImageValid($uploadedFile)) {
                $media = $this->mediaHelper->uploadedImageToMediaEntity($uploadedFile);
                $media->setTrick($trick);
                $this->entityManager->persist($media);
            }
        }
        $this->entityManager->persist($trick);
        $this->entityManager->flush();
    }

    public function delete(Trick $trick)
    {
        $mediaCollection = $trick->getMedia();
        foreach($mediaCollection as $media) {
            $this->entityManager->remove($media);
        }
        $this->entityManager->flush();
        $this->entityManager->remove($trick);
        $this->entityManager->flush();
    }
}
