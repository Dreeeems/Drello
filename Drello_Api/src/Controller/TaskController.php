<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/api/tasks', name: 'api_tasks', methods: ['GET'])]
    public function index(TaskRepository $repository): JsonResponse
    {
        // Récupérer toutes les tâches depuis le repository
        $tasks = $repository->findAll();

        // Convertir les objets Task en tableau de données
        $data = [];
        foreach ($tasks as $task) {
            $data[] = [
                'id' => $task->getId(),
                'title' => $task->getName(),
                'description' => $task->getDescription(),
                'status' => $task->getStatus(),
            ];
        }

        // Retourner les données en JSON
        return $this->json($data);
    }
}
