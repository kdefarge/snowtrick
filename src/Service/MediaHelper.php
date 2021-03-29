<?php

namespace App\Service;

use App\Entity\Trick;
use App\Entity\Media;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaHelper
{
    private $entityManager;
    private $uploadedManager;
    private $embedLinkMaker;
    
    public function __construct(EntityManagerInterface $entityManager, 
        UploadedManager $uploadedManager, EmbedLinkMaker $embedLinkMaker)
    {
        $this->entityManager = $entityManager;
        $this->uploadedManager = $uploadedManager;
        $this->embedLinkMaker = $embedLinkMaker;
    }

    public function newImage(array $uploadedCollection, Trick $trick) : void
    {
        foreach($uploadedCollection as $uploadedFile) {
            if($uploadedFile instanceof UploadedFile 
            && $this->uploadedManager->validateImage($uploadedFile)) {
                $media = new Media();
                $media->setLink($this->uploadedManager->moveAndGetLink($uploadedFile,'trick'));
                $media->setTrick($trick);
                $this->entityManager->persist($media);
            }
        }
        $this->entityManager->flush();
    }

    public function newVideo(array $linkCollection, Trick $trick) : void
    {
        foreach($linkCollection as $link) {
            if($link !== null && $link = $this->embedLinkMaker->create($link)){
                $media = new Media();
                $media->setTrick($trick);
                $media->setLink($link);
                $media->setIsVideoLink(true);
                $this->entityManager->persist($media);
            }
        }
        $this->entityManager->flush();
    }

    public function deleteCollection(Collection $mediaCollection)
    {
        foreach($mediaCollection as $media) {
            /** @var Media $media */
            if(!$media->getIsVideoLink())
                $this->uploadedManager->deleteUploadedFile($media->getLink());
            $this->entityManager->remove($media);
        }
        $this->entityManager->flush();
    }

    public function delete(Media $media)
    {
        $trick = $media->getTrick();
        if(!$media->getIsVideoLink()) {
            $featuredMedia = $trick->getFeaturedMedia();
            if($featuredMedia && $featuredMedia->getId() === $media->getId()) {
                $trick->setFeaturedMedia(null);
                $this->entityManager->persist($trick);
            }
            $this->uploadedManager->deleteUploadedFile($media->getLink());
        }
        $this->entityManager->remove($media);
        $this->entityManager->flush();
    }
}
