<?php

namespace App\Service;

use App\Entity\Task;
use DateTimeImmutable;
use LogicException;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    public function __construct(
        private TaskProviderInterface $provider
    ) {}

    /**
     * Marque une tâche comme terminée si valide
     *
     * @throws LogicException si statut invalide ou expiré
     */
    public function markAsDone(Task $task): void
    {
        if ($task->getStatus() === Task::STATUS_DONE) {
            throw new LogicException('La tâche est déjà terminée');
        }

        $due = $task->getDueDate();
        if ($due && (new DateTimeImmutable() > $due->modify('+7 days'))) {
            throw new LogicException('Impossible de terminer une tâche expirée depuis plus de 7 jours');
        }

        $task->setStatus(Task::STATUS_DONE);
        $this->provider->saveTask($task);
    }

    /**
     * Retourne les tâches d'un projet triées par dueDate puis statut
     *
     * @return Task[]
     */
    public function getPrioritizedTasks(int $projectId): array
    {
        $tasks = $this->provider->getTasksByProject($projectId);
        usort($tasks, function(Task $a, Task $b) {
            $dateA = $a->getDueDate() ?? new DateTimeImmutable('9999-12-31');
            $dateB = $b->getDueDate() ?? new DateTimeImmutable('9999-12-31');
            if ($dateA < $dateB) return -1;
            if ($dateA > $dateB) return 1;
            $order = [Task::STATUS_TODO, Task::STATUS_IN_PROGRESS, Task::STATUS_DONE];
            return array_search($a->getStatus(), $order, true) <=> array_search($b->getStatus(), $order, true);
        });
        return $tasks;
    }
}