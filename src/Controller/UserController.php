<?php

namespace App\Controller;

use App\Form\ProfilPictureFormType;
use App\Form\UserType;
use App\Service\UserHelper;
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
            return $this->redirectToRoute('user_account',['_fragment' => 'header']);
        }

        $form_profilpicture = $this->createForm(ProfilPictureFormType::class);
        $form_profilpicture->handleRequest($request);
        
        if ($form_profilpicture->isSubmitted() && $form_profilpicture->isValid()) {

            $uploadedFile = $form_profilpicture->get('picture')->getData();
            $userHelper->editProfilPicture($user, $uploadedFile);

            return $this->redirectToRoute('user_account');
        }

        return $this->render('user/account.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'form_resetpassword' => $form_resetpassword->createView(),
            'form_profilpicture' => $form_profilpicture->createView(),
        ]);
    }

    /**
     * @Route("/", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, UserHelper $userHelper): Response
    {   
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();

        if ($this->isCsrfTokenValid('delete', $request->get('_token'))) {
            $userHelper->deleteAccount($user);
            $this->get('security.token_storage')->setToken(null);
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/", name="user_delete_profilpicture", methods={"GET"})
     */
    public function deleteProfilPicture(UserHelper $userHelper): Response
    {   
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        $userHelper->deleteProfilPicture($user);
        return $this->redirectToRoute('user_account');
    }
}
