<?php

namespace App\Controller;

use App\Entity\Discussion;
use App\Entity\Media;
use App\Entity\Trick;
use App\Form\DiscussionFormType;
use App\Repository\DiscussionRepository;
use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use App\Service\MediaHelper;
use App\Service\TrickManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
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
    public function new(TrickManager $trickManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $trick = new Trick();
        $trick->setUser($this->getUser());

        $form = $trickManager->new($trick);

        if ($form instanceof Form) {
            return $this->render('trick/new.html.twig', [
                'trick' => $trick,
                'form' => $form->createView(),
            ]);
        }

        return $this->redirectToRoute('trick_show', ['slug' => $trick->getName()]);
    }

    /**
     * @Route("/show/{slug}", name="trick_show", methods={"GET","POST"})
     */
    public function show(string $slug, Request $request, DiscussionRepository $discussionRepository, TrickRepository $trickRepository): Response
    {
        $trick = $trickRepository->findOneJoinedToUserAndCategory($slug);
        if(!$trick)
            throw $this->createNotFoundException('La figure n\'existe pas !');

        $discussion = new Discussion();
        
        $form = $this->createForm(DiscussionFormType::class, $discussion);
        $form->handleRequest($request);

        if ($this->isGranted('ROLE_USER') && $form->isSubmitted() && $form->isValid()) {
            $discussion->setUser($this->getUser());
            $discussion->setTrick($trick);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($discussion);
            $entityManager->flush();

            return $this->redirectToRoute('trick_show', ['slug' => $trick->getName(), '_fragment' => 'discussion-area']);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
            'discussions'  => $discussionRepository->findByTrickJoinedToUser($trick),
        ]);
    }

    /**
     * @Route("/show/{slug}/edit", name="trick_edit", methods={"GET","POST"})
     */
    public function edit(string $slug, TrickRepository $trickRepository, TrickManager $trickManager): Response
    {
        $trick = $trickRepository->findOneJoinedToUserAndCategory($slug);

        if(!($trick instanceof Trick)) {
            throw $this->createNotFoundException('La figure n\'existe pas !');
        }
        
        $user = $trick->getUser();
        $this->denyAccessUnlessGranted('owner', $user);

        $form = $trickManager->edit($trick);

        if ($form instanceof Form) {
            return $this->render(
                'trick/edit.html.twig', [
                    'trick' => $trick,
                    'form' => $form->createView(),
            ]);
        }

        return $this->redirectToRoute('trick_show', ['slug' => $trick->getName()]);
    }

    /**
     * @Route("/{id}", name="trick_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Trick $trick, TrickManager $trickManager): Response
    {
        $user = $trick->getUser();
        $this->denyAccessUnlessGranted('owner', $user);

        if ($this->isCsrfTokenValid('delete', $request->request->get('_token'))) {
            $trickManager->delete($trick);
        }

        return $this->redirectToRoute('homepage', ['_fragment' => 'tricks']);
    }

    /**
     * @Route("/media/{id}", name="media_delete", methods={"DELETE"})
     */
    public function deleteMedia(Request $request, Media $media, MediaHelper $mediaHelper): Response
    {
        $trick = $media->getTrick();
        $user = $trick->getUser();
        $this->denyAccessUnlessGranted('owner', $user);

        if ($this->isCsrfTokenValid('delete', $request->request->get('_token'))) {
            $mediaHelper->delete($media);
        }

        return $this->redirectToRoute('trick_edit', ['slug' => $trick->getName()]);
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
        
        return $this->redirectToRoute('trick_edit', ['slug' => $trick->getName()]);
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
        
        return $this->redirectToRoute('trick_edit', ['slug' => $trick->getName()]);
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

        return $this->redirectToRoute('trick_show', ['slug' => $trick->getName(), '_fragment' => 'discussion-area']);
    }
    
    /**
     * @Route("/checkcover", name="trick_checkcover", methods={"GET"})
     */
    public function checkcover(TrickRepository $trickRepository, MediaRepository $mediaRepository): Response
    {
        $tricks = $trickRepository->findAll();
        foreach($tricks as $trick) {
            $media = $mediaRepository->findOneBy(['trick' => $trick, 'isVideoLink' => false]);
            if ($media instanceof Media) {
                $trick->setFeaturedMedia($media);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($trick);
            }
        }
        $entityManager->flush();
        return $this->redirectToRoute('homepage', ['_fragment' => 'tricks']);
    }
}
