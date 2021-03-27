<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\Media;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;

class TrickHelper
{
    private $entityManager;
    private $uploadedManager;
    private $trickRepository;
    private $embedLinkMaker;
    
    public function __construct(EntityManagerInterface $entityManager, UploadedManager $uploadedManager, 
        TrickRepository $trickRepository, EmbedLinkMaker $embedLinkMaker)
    {
        $this->entityManager = $entityManager;
        $this->uploadedManager = $uploadedManager;
        $this->trickRepository = $trickRepository;
        $this->embedLinkMaker = $embedLinkMaker;
    }

    public function formToDatabase(Trick $trick, Form $form) : void
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
        
        $videolinksCollection = $form->get('videolinks')->getData();
        foreach($videolinksCollection as $link) {
            if($link && $link = $this->embedLinkMaker->create($link)){
                $media = new Media();
                $media->setTrick($trick);
                $media->setLink($link);
                $media->setIsVideoLink(true);
                $this->entityManager->persist($media);
            }
        }

        $this->entityManager->flush();
    }

    public function delete(Trick $trick)
    {
        $category = $trick->getCategory();
        $medias = $trick->getMedia();
        foreach($medias as $media) {
            /** @var Media $media */
            if($media->getIsVideoLink())
                $this->uploadedManager->deleteUploadedFile($media->getLink());
            $this->entityManager->remove($media);
        }
        $trick->setFeaturedMedia(null);
        $this->entityManager->persist($trick);
        $this->entityManager->flush();
        $this->entityManager->remove($trick);
        $this->entityManager->flush();
        $this->checkAndDeleteNotUsedCategory($category);
    }

    public function deleteOneMedia(Media $media)
    {
        $trick = $media->getTrick();
        $featuredMedia = $trick->getFeaturedMedia();
        if($featuredMedia && $featuredMedia->getId() === $media->getId()) {
            $trick->setFeaturedMedia(null);
            $this->entityManager->persist($trick);
        }
        if($media->getIsVideoLink())
            $this->uploadedManager->deleteUploadedFile($media->getLink());
        $this->entityManager->remove($media);
        $this->entityManager->flush();
    }

    public function checkAndDeleteNotUsedCategory(Category $category)
    {
        $trick = $this->trickRepository->findOneBy(['category'=>$category]);
        if ($trick)
            return;
        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }
}
