<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class SimpleFlash
{
    private $session;
    private $flashBag;
    
    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function type_none(string $type, string $message)
    {
        $this->flashBag->add($type, $message);
    }

    public function type_primary(string $message)
    {
        $this->type_none('primary', $message);
    }

    public function type_secondary(string $message)
    {
        $this->type_none('secondary', $message);
    }

    public function type_success(string $message)
    {
        $this->type_none('success', $message);
    }

    public function type_danger(string $message)
    {
        $this->type_none('danger', $message);
    }

    public function type_warning(string $message)
    {
        $this->type_none('warning', $message);
    }

    public function type_info(string $message)
    {
        $this->type_none('info', $message);
    }

    public function type_light(string $message)
    {
        $this->type_none('light', $message);
    }

    public function type_dark(string $message)
    {
        $this->type_none('dark', $message);
    }
}
