<?php

/**
 * A service to manage the media in database
 *
 * The service allows to add a collection of video link or image upload.
 * The deletion of one or a collection of media
 *
 * @author     Kevin DEFARGE <kdefarge@gmail.com>
 */

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

    public function __construct(
        EntityManagerInterface $entityManager,
        UploadedManager $uploadedManager,
        EmbedLinkMaker $embedLinkMaker
    ) {
        $this->entityManager = $entityManager;
        $this->uploadedManager = $uploadedManager;
        $this->embedLinkMaker = $embedLinkMaker;
    }

    /**
     * Add a uploaded image list to a trick save in database
     *
     * @param array $uploadedCollection the collection to be linked to the trick
     * @param Trick $trick the trick that will receive the collection
     * 
     * @return void
     */
    public function newImage(array $uploadedCollection, Trick $trick): void
    {
        foreach ($uploadedCollection as $uploadedFile) {
            if (
                $uploadedFile instanceof UploadedFile
                && $this->uploadedManager->validateImage($uploadedFile)
            ) {
                $media = new Media();
                $media->setLink($this->uploadedManager->moveAndGetLink($uploadedFile, 'trick'));
                $media->setTrick($trick);
                $this->entityManager->persist($media);
            }
        }
        $this->entityManager->flush();
    }

    /**
     * Add a video link list to a trick save in database
     *
     * @param array $linkCollection the collection to be linked to the trick
     * @param Trick $trick the trick that will receive the collection
     * 
     * @return void
     */
    public function newVideo(array $linkCollection, Trick $trick): void
    {
        foreach ($linkCollection as $link) {
            if ($link !== null && $link = $this->embedLinkMaker->create($link)) {
                $media = new Media();
                $media->setTrick($trick);
                $media->setLink($link);
                $media->setIsVideoLink(true);
                $this->entityManager->persist($media);
            }
        }
        $this->entityManager->flush();
    }

    /**
     * Delete a media collection from the database
     *
     * @param Collection $mediaCollection the media collection you want to delete
     * 
     * @return void
     */
    public function deleteCollection(Collection $mediaCollection)
    {
        foreach ($mediaCollection as $media) {
            /** @var Media $media */
            if (!$media->getIsVideoLink())
                $this->uploadedManager->deleteUploadedFile($media->getLink());
            $this->entityManager->remove($media);
        }
        $this->entityManager->flush();
    }

    /**
     * Delete the media from the database
     *
     * @param Media $media the media you want to delete
     * 
     * @return void
     */
    public function delete(Media $media)
    {
        $trick = $media->getTrick();
        if (!$media->getIsVideoLink()) {
            $featuredMedia = $trick->getFeaturedMedia();
            if ($featuredMedia && $featuredMedia->getId() === $media->getId()) {
                $trick->setFeaturedMedia(null);
                $this->entityManager->persist($trick);
            }
            $this->uploadedManager->deleteUploadedFile($media->getLink());
        }
        $this->entityManager->remove($media);
        $this->entityManager->flush();
    }
}
