<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
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
    public function new(Request $request): Response
    {
        if(!$this->isGranted('ROLE_USER')) {
            $this->addFlash('warning', 'user.needed');
            return $this->redirectToRoute('homepage');
        }

        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();

            $trick->setUser($this->getUser());
            $newcategory = $form->get('newcategory')->getData();
            if('' !== $newcategory && null !== $newcategory) {
                $category = new Category();
                $category->setName($newcategory);
                $trick->setCategory($category);
                $entityManager->persist($category);
            }
            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('homepage');
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
    public function edit(Request $request, Trick $trick): Response
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
            $entityManager = $this->getDoctrine()->getManager();
            
            $newcategory = $form->get('newcategory')->getData();
            if('' !== $newcategory && null !== $newcategory) {
                $category = new Category();
                $category->setName($newcategory);
                $trick->setCategory($category);
                $entityManager->persist($category);
                //supprimer les catégories non rataché
            }
            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('trick_index');
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="trick_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Trick $trick): Response
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
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trick);
            $entityManager->flush();
        }

        return $this->redirectToRoute('trick_index');
    }
}
