<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    
    public function load(ObjectManager $manager)
    {
        // DEFAULT USER
        $user = new User();
        $user->setEmail("olivier.chabrol@univ-amu.fr");
        $user->setRoles(array('ROLE_USER')); 
        $user->setPassword($this->passwordEncoder->encodePassword($user,'toto'));

        $manager->persist($user);
        $manager->flush();

        // ADMIN USER
        $user = new User();
        $user->setEmail("admin@admin.com");
        $user->setRoles(array('ROLE_ADMIN'));
        $user->setPassword($this->passwordEncoder->encodePassword($user,'root'));

        $manager->persist($user);
        $manager->flush();
    }
    
}