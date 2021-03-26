<?php

namespace App\Controller;

use App\Form\UserType;
use App\Service\TrickHelper;
use App\Service\UserHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/account", name="user_account", methods={"GET","POST"})
     */
    public function edit(Request $request, UserHelper $userHelper): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('user_account');
        }

        /** @var FormeInterface $form_resetpassword */
        if($userHelper->isMakeProcessResetPasswordForm($form_resetpassword, $this->getUser())) {
            return $this->redirectToRoute('user_account');
        }

        return $this->render('user/account.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'form_resetpassword' => $form_resetpassword->createView(),
        ]);
    }

    /**
     * @Route("/", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, SessionInterface $session, TrickHelper $trickHelp): Response
    {   
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();

        if ($this->isCsrfTokenValid('delete-account', $request->request->get('_token'))) {

            $tricks = $user->getTricks();
            foreach($tricks as $trick)
                $trickHelp->delete($trick);

            $this->get('security.token_storage')->setToken(null);
            $session->invalidate(0);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('danger', 'user.deleted');
            return $this->redirectToRoute('homepage');
        }
    }
}
