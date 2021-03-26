<?php

namespace App\Service;

use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserHelper
{
    private RequestStack $requestStack;
    private FormFactoryInterface $formFacotry;
    private UserPasswordEncoderInterface $userPasswordEncoder;
    private EntityManagerInterface $entityManager;
    private SessionInterface $session;
    private SimpleFlash $simpleFlash;
    
    public function __construct(RequestStack $requestStack, FormFactoryInterface $formFacotry, 
        UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager, 
        SessionInterface $session, SimpleFlash $simpleFlash)
    {
        $this->requestStack = $requestStack;
        $this->formFacotry = $formFacotry;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->simpleFlash = $simpleFlash;
    }

    public function isMakeProcessResetPasswordForm(&$form, $user) : bool
    {
        $form = $this->formFacotry->create(ChangePasswordFormType::class);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted() && $form->isValid()) {

            // Encode the plain password, and set it.
            $encodedPassword = $this->userPasswordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->entityManager->flush();

            // The session is cleaned up after the password has been changed.
            $this->session->remove('ResetPasswordPublicToken');
            $this->session->remove('ResetPasswordCheckEmail');
            $this->session->remove('ResetPasswordToken');
            
            $this->simpleFlash->typeSuccess('le mot de pass a été réinitialisé!');
            return true;
        }

        return false;
    }
}
