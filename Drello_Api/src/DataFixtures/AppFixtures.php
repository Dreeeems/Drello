<?php

namespace App\DataFixtures;

use App\Entity\Projects;
use App\Entity\Task;
use App\Entity\Teams;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->faker = Factory::create('en_En');
    }

    public function load(ObjectManager $manager): void
    {
        // Users creation
        $users = [];
        for ($i = 0; $i < 80; $i++) {
            $user = new User();
            $user->setName($this->faker->name())
                ->setUsername($this->faker->firstName())
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE_USER']);
            $user->setPlainPassword("password");
            $users[] = $user;
            $manager->persist($user);
        }

        // Teams creation
        $teams = [];
        for ($t = 0; $t < 12; $t++) {
            $team = new Teams();
            $team->setName($this->faker->company());
            // add user to teams
            for ($ut = 0; $ut < mt_rand(5, 15); $ut++) {
                $team->addUser($users[mt_rand(0, count($users) - 1)]);
            }
            $teams[] = $team;
            $manager->persist($team);
        }

        // Taks creation
        $tasks = [];
        for ($t = 0; $t < 50; $t++) {
            $task = new Task();
            $task->setName($this->faker->name());
            $task->setDescription($this->faker->words(10, true));
            $task->setStatus($this->faker->word());
            $tasks[] = $task;
            $manager->persist($task);
        }

        // Projects creation
        for ($p = 0; $p < 20; $p++) {
            $project = new Projects();
            $project->setName($this->faker->name());
            
            // Associer un projet à une équipe aléatoire
            $team = $teams[mt_rand(0, count($teams) - 1)];
            $project->setTeam($team);
            
            // Add task
            for ($k = 0; $k < mt_rand(5, 15); $k++) {
                $project->addTask($tasks[mt_rand(0, count($tasks) - 1)]);
            }

            $manager->persist($project);
        }

        // Save in DB
        $manager->flush();
    }
}
