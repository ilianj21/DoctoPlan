<?php

namespace App\Service;

use App\Entity\Project;

interface ProjectProviderInterface
{
    /**
     * Retourne tous les projets
     *
     * @return Project[]
     */
    public function getAllProjects(): array;

    /**
     * Retourne un projet par son ID
     *
     * @param int $projectId
     * @return Project|null
     */
    public function getProjectById(int $projectId): ?Project;

    /**
     * Enregistre un projet
     *
     * @param Project $project
     */
    public function saveProject(Project $project): void;

    /**
     * Supprime un projet
     *
     * @param Project $project
     */
    public function deleteProject(Project $project): void;
}