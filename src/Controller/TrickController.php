<?php

namespace App\Controller;

use App\Entity\Discussion;
use App\Entity\Media;
use App\Entity\Trick;
use App\Form\DiscussionFormType;
use App\Form\TrickType;
use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use App\Service\TrickHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/trick")
 */
class TrickController extends AbstractController
{
    /**
     * @Route("/new", name="trick_new", methods={"GET","POST"})
     */
    public function new(Request $request, TrickHelper $trickHelper, MediaRepository $mediaRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $trick->setUser($this->getUser());
            $trickHelper->formToDatabase($trick, $form);
            
            /** @var Media $media */
            $media = $mediaRepository->findOneBy(['trick' => $trick, 'isVideoLink' => false]);

            if($media) {
                $trick->setFeaturedMedia($media);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($trick);
                $entityManager->flush();
            }

            return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
        }

        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="trick_show", methods={"GET","POST"})
     */
    public function show(Request $request, Trick $trick): Response
    {
        $discussion = new Discussion();
        
        $form = $this->createForm(DiscussionFormType::class, $discussion);
        $form->handleRequest($request);

        if ($this->isGranted('ROLE_USER') && $form->isSubmitted() && $form->isValid()) {
            $discussion->setUser($this->getUser());
            $discussion->setTrick($trick);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($discussion);
            $entityManager->flush();

            return $this->redirectToRoute('trick_show', ['id' => $trick->getId(), '_fragment' => 'discussion-area']);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="trick_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Trick $trick, TrickHelper $trickHelper): Response
    {
        $user = $trick->getUser();
        $this->denyAccessUnlessGranted('owner', $user);
        
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickHelper->formToDatabase($trick, $form);
            return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="trick_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Trick $trick, TrickHelper $trickHelper): Response
    {
        $user = $trick->getUser();
        $this->denyAccessUnlessGranted('owner', $user);

        if ($this->isCsrfTokenValid('delete', $request->request->get('_token'))) {
            $trickHelper->delete($trick);
        }

        return $this->redirectToRoute('homepage', ['_fragment' => 'tricks']);
    }

    /**
     * @Route("/media/{id}", name="media_delete", methods={"DELETE"})
     */
    public function deleteMedia(Request $request, Media $media, TrickHelper $trickHelper): Response
    {
        $trick = $media->getTrick();
        $user = $trick->getUser();
        $this->denyAccessUnlessGranted('owner', $user);

        if ($this->isCsrfTokenValid('delete', $request->request->get('_token'))) {
            $trickHelper->deleteOneMedia($media);
        }

        return $this->redirectToRoute('trick_edit', ['id' => $trick->getId()]);
    }

    /**
     * @Route("/media/{id}/featured", name="trick_update_featuredmedia", methods={"GET"})
     */
    public function updateFeaturedMedia(Media $media): Response
    {
        $trick = $media->getTrick();
        $user = $trick->getUser();
        $this->denyAccessUnlessGranted('owner', $user);

        if(!$media->getIsVideoLink()) {
            $trick->setFeaturedMedia($media);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trick);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('trick_edit', ['id' => $trick->getId()]);
    }

    /**
     * @Route("{id}/featured/remove/", name="trick_remove_featuredmedia", methods={"GET"})
     */
    public function removeFeaturedMedia(Trick $trick): Response
    {
        $user = $trick->getUser();
        $this->denyAccessUnlessGranted('owner', $user);

        $trick->setFeaturedMedia(null);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($trick);
        $entityManager->flush();
        
        return $this->redirectToRoute('trick_edit', ['id' => $trick->getId()]);
    }
    

    /**
     * @Route("/disscusion/{id}", name="discussion_delete", methods={"DELETE"})
     */
    public function deleteDiscussion(Request $request, Discussion $discussion): Response
    {
        $user = $discussion->getUser();
        $this->denyAccessUnlessGranted('owner', $user);

        $trick = $discussion->getTrick();

        if ($this->isCsrfTokenValid('delete', $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($discussion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('trick_show', ['id' => $trick->getId(), '_fragment' => 'discussion-area']);
    }
}
