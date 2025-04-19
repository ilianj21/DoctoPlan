<?php

namespace App\Repository;

use App\Service\ProjectProviderInterface;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineProjectProvider implements ProjectProviderInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    /**
     * @return Project[]
     */
    public function getAllProjects(): array
    {
        return $this->em->getRepository(Project::class)->findAll();
    }

    /**
     * @param int $projectId
     * @return Project|null
     */
    public function getProjectById(int $projectId): ?Project
    {
        return $this->em->getRepository(Project::class)->find($projectId);
    }

    /**
     * @param Project $project
     */
    public function saveProject(Project $project): void
    {
        $this->em->persist($project);
        $this->em->flush();
    }

    /**
     * @param Project $project
     */
    public function deleteProject(Project $project): void
    {
        $this->em->remove($project);
        $this->em->flush();
    }
}