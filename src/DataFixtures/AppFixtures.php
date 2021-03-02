<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\Media;
use App\Entity\Discussion;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $names = ['toto','Gérald de Riv','Moka love','dodo lenfant','James Kenobie','Beth Harmon'];
        $nb = 1;
        foreach ($names as &$name) {
            $user = new User();
            $user->setEmail('email'.$nb++.'@symfony.com');
            $user->setFullname($name);
            $password = $this->encoder->encodePassword($user, 'toctoc');
            $user->setPassword($password);
            $user->setEmailVerified(false);
            $user->setPictureLink('image/default.gif');
            $user->setCreatedDate(new \DateTime());
            $user->setUpdatedDate(new \DateTime());
            $manager->persist($user);
        }
        
        $manager->flush();
    }
}
