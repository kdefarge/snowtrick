<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SimpleFlash
{
    private $flashBag;
    
    public function __construct(SessionInterface $session)
    {
        /** @var Session $session */
        $this->flashBag = $session->getFlashBag();
    }

    public function typeNone(string $type, string $message)
    {
        $this->flashBag->add($type, $message);
    }

    public function typePrimary(string $message)
    {
        $this->typeNone('primary', $message);
    }

    public function typeSecondary(string $message)
    {
        $this->typeNone('secondary', $message);
    }

    public function typeSuccess(string $message)
    {
        $this->typeNone('success', $message);
    }

    public function typeDanger(string $message)
    {
        $this->typeNone('danger', $message);
    }

    public function typeWarning(string $message)
    {
        $this->typeNone('warning', $message);
    }

    public function typeInfo(string $message)
    {
        $this->typeNone('info', $message);
    }

    public function typeLight(string $message)
    {
        $this->typeNone('light', $message);
    }

    public function typeDark(string $message)
    {
        $this->typeNone('dark', $message);
    }
}
