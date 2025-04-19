<?php

namespace App\Service;

use App\Entity\Task;
use DateTimeImmutable;

class RiskAnalyzerService
{
    public function __construct(private TaskProviderInterface $provider) {}

    /**
     * Retourne les tâches à risque (dueDate < 48h, status != done)
     *
     * @return Task[]
     */
    public function getTasksAtRisk(int $projectId): array
    {
        $tasks = $this->provider->getTasksByProject($projectId);
        $now = new DateTimeImmutable();
        $limit = $now->modify('+48 hours');

        return array_filter($tasks, function(Task $task) use ($now, $limit) {
            $due = $task->getDueDate();
            return in_array($task->getStatus(), [Task::STATUS_TODO, Task::STATUS_IN_PROGRESS], true)
                && $due !== null
                && $due >= $now
                && $due <= $limit;
        });
    }
}