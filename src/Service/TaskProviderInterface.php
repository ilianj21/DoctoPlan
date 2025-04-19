<?php

namespace App\Service;

use App\Entity\Task;

interface TaskProviderInterface
{
    /**
     * Retourne toutes les tâches d'un projet
     *
     * @param int $projectId
     * @return Task[]
     */
    public function getTasksByProject(int $projectId): array;

    /**
     * Retourne toutes les tâches
     *
     * @return Task[]
     */
    public function getAllTasks(): array;

    /**
     * Retourne une tâche par son ID
     *
     * @param int $taskId
     * @return Task|null
     */
    public function getTaskById(int $taskId): ?Task;

    /**
     * Retourne toutes les tâches par statut
     *
     * @param string $status
     * @return Task[]
     */
    public function saveTask(Task $task): void;

    /**
     * Retourne toutes les tâches par dueDate
     *
     * @param \DateTimeImmutable $dueDate
     * @return Task[]
     */   
    public function deleteTask(Task $task): void;

}