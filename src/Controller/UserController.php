<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

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
     * @Route("/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if(!$this->isGranted('ROLE_USER')) {

            $this->addFlash('warning', 'user.needed');
            return $this->redirectToRoute('homepage');
        }

        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $encodedPassword = $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_account');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, SessionInterface $session): Response
    {
        if($this->isGranted('ROLE_USER')) {

            $user = $this->getUser();

            if ($this->isCsrfTokenValid('delete-account', $request->request->get('_token'))) {

                $this->get('security.token_storage')->setToken(null);
                $session->invalidate(0);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($user);
                $entityManager->flush();

                $this->addFlash('danger', 'user.deleted');
                return $this->redirectToRoute('homepage');
            }
        }
        
        $this->addFlash('warning', 'user.needed');
        return $this->redirectToRoute('homepage');
    }
}
