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
        if(!$this->isGranted('ROLE_USER')) {
            $this->addFlash('warning', 'user.needed');
            return $this->redirectToRoute('homepage');
        }

        $trick = new Trick();
        $trick->setUser($this->getUser());

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

        if(!$this->isGranted('ROLE_USER')) {
            $this->addFlash('warning', 'user.needed');
            return $this->redirectToRoute('homepage');
        }

        $discussion = new Discussion();
        $form = $this->createForm(DiscussionFormType::class, $discussion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
        if(!$this->isGranted('ROLE_USER')) {
            $this->addFlash('warning', 'user.needed');
            return $this->redirectToRoute('homepage');
        }

        $user = $this->getUser();

        if($user->getId()!=$trick->getUser()->getId()) {
            $this->addFlash('warning', 'user.needed');
            return $this->redirectToRoute('homepage');
        }
        
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
        if(!$this->isGranted('ROLE_USER')) {
            $this->addFlash('warning', 'user.needed');
            return $this->redirectToRoute('homepage');
        }

        $user = $this->getUser();

        if($user->getId()!=$trick->getUser()->getId()) {
            $this->addFlash('warning', 'user.needed');
            return $this->redirectToRoute('homepage');
        }

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
        if(!$this->isGranted('ROLE_USER')) {
            $this->addFlash('warning', 'user.needed');
            return $this->redirectToRoute('homepage');
        }

        $user = $this->getUser();
        $trick = $media->getTrick();
        if($user->getId()!=$trick->getUser()->getId()) {
            $this->addFlash('warning', 'user.needed');
            return $this->redirectToRoute('homepage');
        }

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
        if(!$this->isGranted('ROLE_USER')) {
            $this->addFlash('warning', 'user.needed');
            return $this->redirectToRoute('homepage');
        }

        $user = $this->getUser();
        $trick = $media->getTrick();

        if($media->getIsVideoLink())
            return $this->redirectToRoute('trick_edit', ['id' => $trick->getId()]);

        if($user->getId()!=$trick->getUser()->getId()) {
            $this->addFlash('warning', 'user.needed');
            return $this->redirectToRoute('homepage');
        }

        $trick->setFeaturedMedia($media);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($trick);
        $entityManager->flush();
        
        return $this->redirectToRoute('trick_edit', ['id' => $trick->getId()]);
    }

    /**
     * @Route("{id}/featured/remove/", name="trick_remove_featuredmedia", methods={"GET"})
     */
    public function removeFeaturedMedia(Trick $trick): Response
    {
        if(!$this->isGranted('ROLE_USER')) {
            $this->addFlash('warning', 'user.needed');
            return $this->redirectToRoute('homepage');
        }

        $user = $this->getUser();

        if($user->getId()!=$trick->getUser()->getId()) {
            $this->addFlash('warning', 'user.needed');
            return $this->redirectToRoute('homepage');
        }

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
        if(!$this->isGranted('ROLE_USER')) {
            $this->addFlash('warning', 'user.needed');
            return $this->redirectToRoute('homepage');
        }

        $user = $this->getUser();

        if($user->getId()!=$discussion->getUser()->getId()) {
            $this->addFlash('warning', 'user.needed');
            return $this->redirectToRoute('homepage');
        }

        $trick = $discussion->getTrick();

        if ($this->isCsrfTokenValid('delete', $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($discussion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('trick_show', ['id' => $trick->getId(), '_fragment' => 'discussion-area']);
    }
}
