<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/error")
 */
class ErrorController extends AbstractController
{
    /**
     * @Route("/authentication", name="error_authentication")
     */
    public function index(): Response
    {
        return $this->render('errors/authentication.html.twig');
    }
}
