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
        $users = [];
        foreach ($names as &$name) {
            $user = new User();
            $users[] = $user;
            $user->setEmail('email'.count($users).'@symfony.com');
            $user->setFullname($name);
            $password = $this->encoder->encodePassword($user, 'toctoc');
            $user->setPassword($password);
            $user->setEmailVerified(false);
            $user->setPictureLink('image/default.gif');
            $user->setCreatedDate(new \DateTime());
            $user->setUpdatedDate(new \DateTime());
            $manager->persist($user);
        }

        $names = ['Les grabs','Les rotations','Old school'];
        $categories = [];
        foreach ($names as &$name) {
            $category = new Category();
            $categories[] = $category;
            $category->setName($name);
            $manager->persist($category);
        }

        $trick = new Trick();
        $trick->setUser($users[array_rand($users)]);
        $trick->setCategory($categories[0]);
        $trick->setName('mute');
        $trick->setContent('saisie de la carre frontside de la planche entre les deux pieds avec la main avant');
        $trick->setCreatedDate(new \DateTime());
        $trick->setUpdatedDate(new \DateTime());
        $manager->persist($trick);
        $this->disscussionFixtures($manager,$trick,$users);

        $trick = new Trick();
        $trick->setUser($users[array_rand($users)]);
        $trick->setCategory($categories[0]);
        $trick->setName('indy');
        $trick->setContent('saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière');
        $trick->setCreatedDate(new \DateTime());
        $trick->setUpdatedDate(new \DateTime());
        $manager->persist($trick);
        $this->disscussionFixtures($manager,$trick,$users);

        $trick = new Trick();
        $trick->setUser($users[array_rand($users)]);
        $trick->setCategory($categories[0]);
        $trick->setName('tail grab');
        $trick->setContent('saisie de la partie arrière de la planche, avec la main arrière');
        $trick->setCreatedDate(new \DateTime());
        $trick->setUpdatedDate(new \DateTime());
        $manager->persist($trick);
        $this->disscussionFixtures($manager,$trick,$users);

        $trick = new Trick();
        $trick->setUser($users[array_rand($users)]);
        $trick->setCategory($categories[0]);
        $trick->setName('nose grab');
        $trick->setContent('saisie de la partie avant de la planche, avec la main avant');
        $trick->setCreatedDate(new \DateTime());
        $trick->setUpdatedDate(new \DateTime());
        $manager->persist($trick);
        $this->disscussionFixtures($manager,$trick,$users);

        $trick = new Trick();
        $trick->setUser($users[array_rand($users)]);
        $trick->setCategory($categories[0]);
        $trick->setName('seat belt');
        $trick->setContent('saisie du carre frontside à l\'arrière avec la main avant');
        $trick->setCreatedDate(new \DateTime());
        $trick->setUpdatedDate(new \DateTime());
        $manager->persist($trick);
        $this->disscussionFixtures($manager,$trick,$users);

        $trick = new Trick();
        $trick->setUser($users[array_rand($users)]);
        $trick->setCategory($categories[0]);
        $trick->setName('truck driver');
        $trick->setContent('saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture)');
        $trick->setCreatedDate(new \DateTime());
        $trick->setUpdatedDate(new \DateTime());
        $manager->persist($trick);
        $this->disscussionFixtures($manager,$trick,$users);

        $trick = new Trick();
        $trick->setUser($users[array_rand($users)]);
        $trick->setCategory($categories[1]);
        $trick->setName('un 180');
        $trick->setContent('un 180 désigne un demi-tour, soit 180 degrés d\'angle ');
        $trick->setCreatedDate(new \DateTime());
        $trick->setUpdatedDate(new \DateTime());
        $manager->persist($trick);
        $this->disscussionFixtures($manager,$trick,$users);

        $trick = new Trick();
        $trick->setUser($users[array_rand($users)]);
        $trick->setCategory($categories[1]);
        $trick->setName('360');
        $trick->setContent('360, trois six pour un tour complet');
        $trick->setCreatedDate(new \DateTime());
        $trick->setUpdatedDate(new \DateTime());
        $manager->persist($trick);
        $this->disscussionFixtures($manager,$trick,$users);

        $trick = new Trick();
        $trick->setUser($users[array_rand($users)]);
        $trick->setCategory($categories[2]);
        $trick->setName('Backside Air');
        $trick->setContent('Backside Air');
        $trick->setCreatedDate(new \DateTime());
        $trick->setUpdatedDate(new \DateTime());
        $manager->persist($trick);
        $this->disscussionFixtures($manager,$trick,$users);

        $trick = new Trick();
        $trick->setUser($users[array_rand($users)]);
        $trick->setCategory($categories[2]);
        $trick->setName('Method Air');
        $trick->setContent('Method Air');
        $trick->setCreatedDate(new \DateTime());
        $trick->setUpdatedDate(new \DateTime());
        $manager->persist($trick);
        $this->disscussionFixtures($manager,$trick,$users);

        $manager->flush();
    }

    private $lorems = [
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit. ',
        'Etiam eget congue massa, vel tempus enim. ',
        'Vivamus blandit massa tincidunt felis pharetra, eu volutpat eros sodales. ',
        'Nam facilisis eros dapibus, fringilla ligula quis, consectetur lectus. ',
        'Donec et interdum risus, at sollicitudin nisi. ',
        'Donec vitae purus auctor, laoreet risus non, volutpat dolor. ',
        'Fusce vitae dui at neque euismod pulvinar ac vitae metus. ',
        'Ut posuere diam eget magna rhoncus interdum. ',
        'Nulla volutpat libero nisi, ut auctor nisi scelerisque at. Nunc lacinia semper nisl.'."\n"
    ];

    private function randomText() : string
    {
        $text='';
        $sentence_nb = mt_rand(1,20);
        for($i=0;$i<$sentence_nb;$i++) {
            $text.=$this->lorems[array_rand($this->lorems)];
        }
        $text=trim($text);
        return $text;
    }

    private function disscussionFixtures(ObjectManager $manager, Trick $trick, Array $users) : void
    {
        $discussion_nb = mt_rand(1,20);
        $datetime = new \DateTime();
        for($i=0;$i<$discussion_nb;$i++) {
            $discussion = new Discussion();
            $discussion->setUser($users[array_rand($users)]);
            $discussion->setTrick($trick);
            $discussion->setMessage($this->randomText());
            $datetime->modify('+'.mt_rand(3,1440).' minutes');
            $discussion->setCreatedDate($datetime);
            $datetime = clone $datetime;
            $manager->persist($discussion);
        }
    }
}
