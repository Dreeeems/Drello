<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{  private Generator $faker;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this-> faker = Factory::create('en_En');
    }
    public function load(ObjectManager $manager): void
    {
      
        for($i=0;$i<10;$i++){
            $user = new User();
            $user->setName($this->faker->name())
            -> setUsername($this->faker->firstName())
            -> setEmail($this->faker->email())
            -> setRoles(['ROLE USER']);
            $user->setPlainPassword("password");
            $manager->persist($user);

        }

       
        $manager->flush();
    }
}
