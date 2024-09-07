<?php

namespace App\Controller;

use App\Entity\Teams;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TeamController extends AbstractController
{
   

    #[Route('/team', name: 'create_team')]
    public function new(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true); 
        $name = $data['name'] ?? null;
        $user = $this->getUser();
        if(!$user){
            return $this->json(['error' => 'User not authenticated'], 403);
        }
        $team = new Teams();
        $team ->setName($name)
        ->setCreator($user)
        -> addAdmin($user);
        $entityManager->persist($team);
        $entityManager->flush();


        return $this->json([
            'message' => 'Team created',
            'team' => [
                'id' => $team->getId(),
                'name' => $team->getName(),
                'team' => $team->getName(),
                'creator' => $team ->getCreator()
            ]
        ], 201);
}
}