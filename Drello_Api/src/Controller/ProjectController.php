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
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    // Get all projects
    #[Route('api/projects', name: 'get_all_projects', methods: ['GET'])]
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

    // Create a project
    #[Route('api/project', name: 'create_project', methods: ['POST'])]
    public function new(Request $request, TeamsRepository $teamsRepository, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true); 
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

    // Update a project
    #[Route('api/project/{id}', name: 'update_project', methods: ['PUT'])]
    public function update(int $id, Request $request, TeamsRepository $teamsRepository, ProjectsRepository $projectsRepository, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $name = $data['name'] ?? null;
        $teamId = $data['team_id'] ?? null;
        $project = $projectsRepository->find($id);

        if (!$project) {
            return $this->json(['error' => 'Project not found'], 404);
        }

        if ($teamId !== null) {
            if (!is_numeric($teamId)) {
                return $this->json(['error' => 'Invalid team ID'], 400);
            }

            $team = $teamsRepository->find($teamId);

            if (!$team) {
                return $this->json(['error' => 'Team not found'], 404);
            }
            $project->setTeam($team);
        }

        if ($name !== null && $name !== "") {
            $project->setName($name);
        }

        $entityManager->flush();
        $user = $this->getUser();

        if (!$team->getAdmins()->contains($user)) {
            return $this->json(['error' => 'You do not have permission to delete this project'], 403);
        }

        return $this->json([
            'message' => 'Project updated successfully',
            'project' => [
                'id' => $project->getId(),
                'name' => $project->getName(),
                'team' => $project->getTeam() ? $project->getTeam()->getName() : null
            ]
        ], 200);
    }

    // Delete a project
    #[Route('api/project/{id}', name: 'delete_project', methods: ['DELETE'])]
    public function delete(int $id, ProjectsRepository $projectsRepository, EntityManagerInterface $entityManager): Response
    {
        $project = $projectsRepository->find($id);
        if (!$project) {
            return $this->json(['error' => 'Project not found'], 404);
        }
        $user = $this->getUser();
        $team = $project->getTeam();
        if (!$team->getAdmins()->contains($user)) {
            return $this->json(['error' => 'You do not have permission to delete this project'], 403);
        }
        // Delete all project tasks
        foreach ($project->getTasks() as $task) {
            $entityManager->remove($task);
        }
        $entityManager->remove($project);
        $entityManager->flush();

        return $this->json(['message' => 'Project deleted!'], 204);
    }
}
