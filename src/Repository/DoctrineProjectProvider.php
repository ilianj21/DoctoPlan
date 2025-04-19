<?php

namespace App\Repository;

use App\Service\ProjectProviderInterface;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineProjectProvider implements ProjectProviderInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function getAllProjects(): array
    {
        return $this->em->getRepository(Project::class)->findAll();
    }

    public function getProjectById(int $projectId): ?Project
    {
        return $this->em->getRepository(Project::class)->find($projectId);
    }

    public function saveProject(Project $project): void
    {
        $this->em->persist($project);
        $this->em->flush();
    }

    public function deleteProject(Project $project): void
    {
        $this->em->remove($project);
        $this->em->flush();
    }
}