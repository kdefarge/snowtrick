<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;

class TrickHelper
{
    private $entityManager;
    private $uploadedManager;
    
    public function __construct(EntityManagerInterface $entityManager, UploadedManager $uploadedManager)
    {
        $this->entityManager = $entityManager;
        $this->uploadedManager = $uploadedManager;
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
        
        $this->entityManager->persist($trick);

        $uploadedFileCollection = $form->get('medias')->getData();
        foreach($uploadedFileCollection as $uploadedFile) {
            if($uploadedFile) {
                $media = $this->uploadedManager->imageValidToMediaEntity($uploadedFile, 'trick');
                if($media) {
                    $media->setTrick($trick);
                    $this->entityManager->persist($media);
                }
            }
        }
        $this->entityManager->flush();
    }

    public function delete(Trick $trick)
    {
        $mediaCollection = $trick->getMedia();
        foreach($mediaCollection as $media) {
            $this->uploadedManager->deleteUploadedFile($media);
            $this->entityManager->remove($media);
        }
        $this->entityManager->flush();
        $this->entityManager->remove($trick);
        $this->entityManager->flush();
    }
}
