<?php

namespace App\Security;

use App\Service\SimpleFlash;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class AuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    private $urlGenerator;
    private $simpleFlash;

    public function __construct(UrlGeneratorInterface $urlGenerator, SimpleFlash $simpleFlash)
    {
        $this->urlGenerator = $urlGenerator;
        $this->simpleFlash = $simpleFlash;
    }

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        $this->simpleFlash->typeDanger('Vous devez vous connecter pour accéder à cette page.');
        return new RedirectResponse($this->urlGenerator->generate('error_authentication'));
    }
}
