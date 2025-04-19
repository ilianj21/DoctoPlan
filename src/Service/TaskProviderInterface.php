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
     * Enregistre une tâche
     *
     * @param Task $task
     */
    public function saveTask(Task $task): void;

    /**
     * Supprime une tâche
     *
     * @param Task $task
     */  
    public function deleteTask(Task $task): void;

}