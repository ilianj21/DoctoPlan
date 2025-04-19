<?php

namespace App\Service;

use App\Entity\Project;
use LogicException;
use Doctrine\ORM\EntityManagerInterface;

class ProjectService
{
    /**
     * @param ProjectProviderInterface $provider
     */
    public function __construct(
        private ProjectProviderInterface $provider
    ) {}

    /**
     * Crée un nouveau projet
     */
    public function createProject(string $name, ?string $description): Project
    {
        if (empty(trim($name))) {
            throw new LogicException('Le nom du projet est obligatoire');
        }

        $project = new Project($name, $description);
        $this->provider->saveProject($project);
        return $project;
    }

    /**
     * Retourne tous les projets
     * @return Project[]
     */
    public function listProjects(): array
    {
        return $this->provider->getAllProjects();
    }
}