<?php

namespace App\DataFixtures\Providers;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FixturesHelperProviders
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function helpEncodePassword(string $plainPassword): string
    {
        return $this->encoder->encodePassword(new User(), $plainPassword);
    }

}
