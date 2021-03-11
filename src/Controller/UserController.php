<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/account", name="user_account", methods={"GET"})
     */
    public function show(): Response
    {
        if($this->isGranted('ROLE_USER')) {
            return $this->render('user/show.html.twig', ['user' => $this->getUser()]);
        }

        $this->addFlash('warning', 'user.needed');
        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request): Response
    {
        if($this->isGranted('ROLE_USER')) {

            $user = $this->getUser();

            return $this->render('user/show.html.twig', ['user' => $this->getUser()]);
            if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($user);
                $entityManager->flush();

                $token = $this->get('security.token_storage')->getToken()->setAuthenticated(false);

                $this->addFlash('danger', 'user.deleted');
                return $this->redirectToRoute('homepage');
            }
        }
        
        $this->addFlash('warning', 'user.needed');
        return $this->redirectToRoute('homepage');
    }
}
