<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\TaskService;
use App\Service\RiskAnalyzerService;
use App\Service\TaskProviderInterface;

#[Route('/api/tasks')]
class TaskController extends AbstractController
{
    public function __construct(
        private TaskService $taskService,
        private RiskAnalyzerService $riskService,
        private TaskProviderInterface $provider
    ) {}

    #[Route('/{projectId}/prioritized', methods: ['GET'])]
    public function listPrioritized(int $projectId): JsonResponse
    {
        $tasks = $this->taskService->getPrioritizedTasks($projectId);
        return $this->json($tasks);
    }

    #[Route('/{projectId}/atrisk', methods: ['GET'])]
    public function listAtRisk(int $projectId): JsonResponse
    {
        $tasks = $this->riskService->getTasksAtRisk($projectId);
        return $this->json($tasks);
    }

    #[Route('/{id}/complete', methods: ['POST'])]
    public function complete(int $id): JsonResponse
    {
        $task = $this->provider->getTaskById($id);
        if (!$task) {
            return $this->json(['error' => 'Tâche non trouvée'], Response::HTTP_NOT_FOUND);
        }

        try {
            $this->taskService->markAsDone($task);
            return $this->json(['success' => true]);
        } catch (\LogicException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}