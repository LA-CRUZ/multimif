<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user_enseignant = new User();
        $user_enseignant->setEmail("enseignant@enseignant.com");
        $user_enseignant->setUsername("prof");
        $password_enseignant = $this->encoder->encodePassword($user_enseignant, 'enseignant');
        $user_enseignant->setPassword($password_enseignant);
        $user_enseignant->addRole("ROLE_TEACHER");
        $manager->persist($user_enseignant);

        $user_enseignant2 = new User();
        $user_enseignant2->setEmail("enseignant2@enseignant.com");
        $user_enseignant2->setUsername("prof2");
        $password_enseignant2 = $this->encoder->encodePassword($user_enseignant2, 'enseignant2');
        $user_enseignant2->setPassword($password_enseignant);
        $user_enseignant2->addRole("ROLE_TEACHER");
        $manager->persist($user_enseignant2);

        $user_eleve = new User();
        $user_eleve->setEmail("eleve@eleve.com");
        $user_eleve->setUsername("eleve");
        $password_eleve = $this->encoder->encodePassword($user_eleve, 'eleve');
        $user_eleve->setPassword($password_eleve);
        $user_eleve->addRole("ROLE_USER");
        $manager->persist($user_eleve);
        
        $manager->flush();
    }
}
