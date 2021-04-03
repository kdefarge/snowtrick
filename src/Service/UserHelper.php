<?php

namespace App\Service;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Repository\ResetPasswordRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserHelper
{
    private $requestStack;
    private $formFacotry;
    private $userPasswordEncoder;
    private $entityManager;
    private $session;
    private $simpleFlash;
    private $uploadedManager;
    private $trickManager;
    private $resetPwRepository;
    
    public function __construct(RequestStack $requestStack, FormFactoryInterface $formFacotry, 
        UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager, 
        SessionInterface $session, SimpleFlash $simpleFlash, UploadedManager $uploadedManager,
        TrickManager $trickManager, ResetPasswordRequestRepository $resetPwRepository)
    {
        $this->requestStack = $requestStack;
        $this->formFacotry = $formFacotry;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
        $this->session = $session;
        $this->simpleFlash = $simpleFlash;
        $this->uploadedManager = $uploadedManager;
        $this->trickManager = $trickManager;
        $this->resetPwRepository = $resetPwRepository;
    }

    public function isMakeProcessResetPasswordForm(&$form, User $user) : bool
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
            
            $this->simpleFlash->typeSuccess('user.flash.success.resetpassword');
            return true;
        }

        return false;
    }

    private function deleteProfilePictureFileIfExist(User $user) {
        if($link = $user->getPictureLink())
            $this->uploadedManager->deleteUploadedFile($link);
    }

    public function editProfilPicture(User $user, $uploadedFile) : void
    {
        $this->deleteProfilePictureFileIfExist($user);

        $link = $this->uploadedManager->moveAndGetLink($uploadedFile, 'profilpicture');
        $user->setPictureLink($link);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function deleteProfilPicture(User $user) : void
    {
        $this->deleteProfilePictureFileIfExist($user);

        $user->setPictureLink(null);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function deleteAccount(User $user)
    {
        $tricks = $user->getTricks();
        foreach($tricks as $trick)
            $this->trickManager->delete($trick);

        $this->deleteProfilePictureFileIfExist($user);

        $this->session->invalidate(0);

        $resets = $this->resetPwRepository->findBy(['user' => $user]);
        foreach($resets as $reset) {
            $this->entityManager->remove($reset);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->simpleFlash->typeDanger('user.flash.warning.deleted');
    }
}
