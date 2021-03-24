<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\Session;

class SimpleFlash
{
    private $session;
    
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function basic(string $type, string $message)
    {
        $this->session->getFlashBag()->add($type, $message);
    }

    public function primary(string $message)
    {
        $this->basic('primary', $message);
    }

    public function secondary(string $message)
    {
        $this->basic('secondary', $message);
    }

    public function success(string $message)
    {
        $this->basic('success', $message);
    }

    public function danger(string $message)
    {
        $this->basic('danger', $message);
    }

    public function warning(string $message)
    {
        $this->basic('warning', $message);
    }

    public function info(string $message)
    {
        $this->basic('info', $message);
    }

    public function light(string $message)
    {
        $this->basic('light', $message);
    }

    public function dark(string $message)
    {
        $this->basic('dark', $message);
    }
}
