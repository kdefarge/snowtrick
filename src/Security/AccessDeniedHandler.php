<?php

namespace App\Security;

use App\Service\SimpleFlash;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private $urlGenerator;
    private $simpleFlash;

    public function __construct(UrlGeneratorInterface $urlGenerator, SimpleFlash $simpleFlash)
    {
        $this->urlGenerator = $urlGenerator;
        $this->simpleFlash = $simpleFlash;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $this->simpleFlash->typeDanger('Vous n\'avez pas accès à cette page.');
        return new RedirectResponse($this->urlGenerator->generate('error_authentication'));
    }
}
