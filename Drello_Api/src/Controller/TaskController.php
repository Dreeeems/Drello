<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\TeamsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    #[Route('/api/tasks', name: 'api_tasks', methods: ['GET'])]
    public function index(TaskRepository $repository): JsonResponse
    {

        $tasks = $repository->findAll();


        $data = [];
        foreach ($tasks as $task) {
            $data[] = [
                'id' => $task->getId(),
                'title' => $task->getName(),
                'description' => $task->getDescription(),
                'status' => $task->getStatus(),
            ];
        }

        return $this->json($data);
    }

    #[Route('api/task', name: 'create_task', methods: ['POST'])]
    public function new(Request $request, TeamsRepository $teamsRepository, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'] ?? null;
        $description = $data['description'] ?? null;
        $assignedIds = $data['assigned'] ?? [];
        $teamId = $data['team_id'] ?? null;
        $status = $data['status'] ?? 'pending';
        $creator = $this->security->getUser();
    
        if (!$creator) {
            return $this->json(['error' => 'User not authenticated'], 403);
        }
    
        if (!$name) {
            return $this->json(['error' => 'Task name is required'], 400);
        }
    
        if (!$teamId || !is_numeric($teamId)) {
            return $this->json(['error' => 'Invalid team ID'], 400);
        }
    
        $team = $teamsRepository->find($teamId);
        if (!$team) {
            return $this->json(['error' => 'Team not found'], 404);
        }
    

        if (!$team->getUsers()->contains($creator)) {
            return $this->json(['error' => 'This user is not in the team'], 403);
        }
    
        $task = new Task();
        $task->setName($name)
             ->setDescription($description)
             ->setCreator($creator)
             ->setStatus($status)
             ->setTeam($team);
    
      
        $assignedUsers = new \Doctrine\Common\Collections\ArrayCollection();
    
        foreach ($assignedIds as $userId) {
            $assignedUser = $userRepository->find($userId);
            if ($assignedUser && $team->getUsers()->contains($assignedUser)) {
                if (!$assignedUsers->contains($assignedUser)) {
                    $assignedUsers->add($assignedUser);
                    $task->addAssigned($assignedUser);
                }
            } else {
                return $this->json(['error' => 'Assigned user with ID ' . $userId . ' is not in the team or not found'], 404);
            }
        }
    
        $entityManager->persist($task);
        $entityManager->flush();
    
        return $this->json([
            'message' => 'Task created successfully',
            'task' => [
                'name' => $task->getName(),
                'team' => $team->getName(),
                'creator' => $creator->getUsername(),
                'assigned' => array_map(function ($user) {
                    return [
                        'id' => $user->getId(),
                        'username' => $user->getUsername(),
                    ];
                }, $task->getAssigned()->toArray()), 
            ]
        ], 201);
    }
    
}
