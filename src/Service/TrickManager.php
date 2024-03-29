<?php

/**
 * A service to create, modify and delete media in the database
 *
 * @author     Kevin DEFARGE <kdefarge@gmail.com>
 */

namespace App\Service;

use App\Entity\Category;
use App\Entity\Media;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class TrickManager
{
    private $entityManager;
    private $categoryHelper;
    private $mediaHelper;
    private $requestStack;
    private $formFacotry;
    private $mediaRepository;
    private $simpleFlash;

    public function __construct(
        EntityManagerInterface $entityManager,
        UploadedManager $uploadedManager,
        MediaHelper $mediaHelper,
        EmbedLinkMaker $embedLinkMaker,
        CategoryHelper $categoryHelper,
        RequestStack $requestStack,
        FormFactoryInterface $formFacotry,
        MediaRepository $mediaRepository,
        SimpleFlash $simpleFlash
    ) {
        $this->entityManager = $entityManager;
        $this->uploadedManager = $uploadedManager;
        $this->embedLinkMaker = $embedLinkMaker;
        $this->categoryHelper = $categoryHelper;
        $this->mediaHelper = $mediaHelper;
        $this->requestStack = $requestStack;
        $this->formFacotry = $formFacotry;
        $this->mediaRepository = $mediaRepository;
        $this->simpleFlash = $simpleFlash;
    }

    /**
     * Create a trick in the database from the form
     *
     * @param Trick $trick the trick that will retrieve the data from the form and be added to the database
     * 
     * @return null|form return the form if it is not valid
     */
    public function new(Trick $trick): ?Form
    {
        $form = $this->formProcess($trick);
        if ($form instanceof Form)
            return $form;

        $media = $this->mediaRepository->findOneBy(['trick' => $trick, 'isVideoLink' => false]);
        if ($media instanceof Media) {
            $trick->setFeaturedMedia($media);
            $this->entityManager->persist($trick);
            $this->entityManager->flush();
        }

        return null;
    }

    /**
     * Update a trick in the database from the form
     *
     * @param Trick $trick the trick that will retrieve the data from the form and update the database
     * 
     * @return null|form return the form if it is not valid
     */
    public function edit(Trick $trick): ?Form
    {
        $trick->setUpdatedDate(new \DateTime());

        $category = $trick->getCategory();

        $form = $this->formProcess($trick);
        if ($form instanceof Form)
            return $form;

        $this->categoryHelper->deleteNotUsedCategory($category);

        return null;
    }

    /**
     * Destroy the trick and the linked data in the database
     *
     * @param Trick $trick The trick that will be deleted
     * 
     * @return null|form return the form if it is not valid
     */
    public function delete(Trick $trick)
    {
        $trick->setFeaturedMedia(null);
        $this->entityManager->flush();

        $this->mediaHelper->deleteCollection($trick->getMedia());

        $category = $trick->getCategory();
        $this->entityManager->remove($trick);
        $this->entityManager->flush();

        $this->categoryHelper->deleteNotUsedCategory($category);
    }

    private function formProcess(Trick $trick): ?Form
    {
        $form = $this->formFacotry->create(TrickType::class, $trick);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        if (!$form->isSubmitted() || !$form->isValid())
            return $form;

        $newcategory = $form->get('newcategory')->getData();

        if ('' !== $newcategory && null !== $newcategory) {

            $category = $this->categoryHelper->findOrCreateCategory($newcategory);

            if ($category instanceof Category) {
                $trick->setCategory($category);
            }
        }
        
        if(!$trick->getCategory()) {
            $this->simpleFlash->typeDanger('trick.flash.error.nocategory');
            return $form;
        }

        $this->entityManager->persist($trick);
        $this->entityManager->flush();

        $uploadedCollection = $form->get('pictures')->getData();
        $this->mediaHelper->newImage($uploadedCollection, $trick);

        $linkCollection = $form->get('videolinks')->getData();
        $this->mediaHelper->newVideo($linkCollection, $trick);

        return null;
    }
}
