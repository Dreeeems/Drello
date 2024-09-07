<?php

namespace App\Controller;

use App\Entity\Projects;
use App\Repository\ProjectsRepository;
use App\Repository\TeamsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProjectController extends AbstractController
{

    //Get all projects
    #[Route('api/projects', name: 'app_project', methods:['GET'])]
    public function index(ProjectsRepository $projectsRepository): JsonResponse
    {
        $projects = $projectsRepository->findAll();

        $data = [];
        foreach ($projects as $project) {

            $team = $project->getTeam();
            $teamData = $team ? [
                'id' => $team->getId(),
                'name' => $team->getName(),
            ] : null;


            $tasksData = [];
            foreach ($project->getTasks() as $task) {
                $tasksData[] = [
                    'id' => $task->getId(),
                    'name' => $task->getName(),
                    'description' => $task->getDescription(),
                    'status' => $task->getStatus(),
                ];
            }


            $data[] = [
                'id' => $project->getId(),
                'title' => $project->getName(),
                'team' => $teamData,  
                'tasks' => $tasksData,
            ];
        }


        return $this->json($data);
    }
    #[Route('api/project', name: 'create_project', methods: ['POST'])]
    public function new(Request $request, TeamsRepository $teamsRepository, EntityManagerInterface $entityManager): Response
    {
        $data = $request->toArray(); 
        $name = $data['name'] ?? null;
        $teamId = $data['team_id'] ?? null;
    
  
    
        if (!is_numeric($teamId)) {
            return $this->json(['error' => 'Invalid team ID'], 400);
        }
    
        $team = $teamsRepository->find($teamId);
    
        if (!$team) {
            return $this->json(['error' => 'Team not found'], 404);
        }
    
        $project = new Projects();
        $project->setName($name);
        $project->setTeam($team);
        $entityManager->persist($project);
        $entityManager->flush();
    
        return $this->json([
            'message' => 'Project created successfully',
            'project' => [
                'id' => $project->getId(),
                'name' => $project->getName(),
                'team' => $team->getName()
            ]
        ], 201);
    }
}