<?php

/**
 * A service to display flash messages
 *
 * @author     Kevin DEFARGE <kdefarge@gmail.com>
 */

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

    /**
     * Display a message with a type that we define
     *
     * @param string $type The type we want
     * @param string $message The message you want to display
     * 
     * @return void
     */
    public function typeNone(string $type, string $message)
    {
        $this->flashBag->add($type, $message);
    }

    /**
     * Display a primary message
     *
     * @param string $message The message you want to display
     * 
     * @return void
     */
    public function typePrimary(string $message)
    {
        $this->typeNone('primary', $message);
    }

    /**
     * Display a secondary message
     *
     * @param string $message The message you want to display
     * 
     * @return void
     */
    public function typeSecondary(string $message)
    {
        $this->typeNone('secondary', $message);
    }

    /**
     * Display a success message
     *
     * @param string $message The message you want to display
     * 
     * @return void
     */
    public function typeSuccess(string $message)
    {
        $this->typeNone('success', $message);
    }

    /**
     * Display a danger message
     *
     * @param string $message The message you want to display
     * 
     * @return void
     */
    public function typeDanger(string $message)
    {
        $this->typeNone('danger', $message);
    }

    /**
     * Display a warning message
     *
     * @param string $message The message you want to display
     * 
     * @return void
     */
    public function typeWarning(string $message)
    {
        $this->typeNone('warning', $message);
    }

    /**
     * Display a info message
     *
     * @param string $message The message you want to display
     * 
     * @return void
     */
    public function typeInfo(string $message)
    {
        $this->typeNone('info', $message);
    }

    /**
     * Display a light message
     *
     * @param string $message The message you want to display
     * 
     * @return void
     */
    public function typeLight(string $message)
    {
        $this->typeNone('light', $message);
    }

    /**
     * Display a dark message
     *
     * @param string $message The message you want to display
     * 
     * @return void
     */
    public function typeDark(string $message)
    {
        $this->typeNone('dark', $message);
    }
}
