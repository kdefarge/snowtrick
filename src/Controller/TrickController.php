<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Trick;
use App\Form\TrickType;
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
     * @Route("/", name="trick_index", methods={"GET"})
     */
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('trick/index.html.twig', [
            'tricks' => $trickRepository->findBy([],['id' => 'DESC']),
        ]);
    }

    /**
     * @Route("/new", name="trick_new", methods={"GET","POST"})
     */
    public function new(Request $request, TrickHelper $trickHelper): Response
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
            $trickHelper->FormToDatabase($trick, $form);
            return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
        }

        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="trick_show", methods={"GET"})
     */
    public function show(Trick $trick): Response
    {
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
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
            $trickHelper->FormToDatabase($trick, $form);
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
}
