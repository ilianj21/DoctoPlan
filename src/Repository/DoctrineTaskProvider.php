<?php

namespace App\Repository;

use App\Service\TaskProviderInterface;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineTaskProvider implements TaskProviderInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function getTasksByProject(int $projectId): array
    {
        return $this->em->getRepository(Task::class)
            ->createQueryBuilder('t')
            ->andWhere('t.project = :proj')
            ->setParameter('proj', $projectId)
            ->getQuery()
            ->getResult();
    }
    public function getAllTasks(): array
    {
        return $this->em->getRepository(Task::class)->findAll();
    }
    public function getTaskById(int $taskId): ?Task
    {
        return $this->em->getRepository(Task::class)->find($taskId);
    }
    public function saveTask(Task $task): void
    {
        $this->em->persist($task);
        $this->em->flush();
    }
    public function deleteTask(Task $task): void
    {
        $this->em->remove($task);
        $this->em->flush();
    }
    public function getTasksByStatus(string $status): array
    {
        return $this->em->getRepository(Task::class)
            ->createQueryBuilder('t')
            ->andWhere('t.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }
    public function getTasksByDueDate(\DateTimeImmutable $dueDate): array
    {
        return $this->em->getRepository(Task::class)
            ->createQueryBuilder('t')
            ->andWhere('t.dueDate = :dueDate')
            ->setParameter('dueDate', $dueDate)
            ->getQuery()
            ->getResult();
    }
    public function getTasksByCreatedAt(\DateTimeImmutable $createdAt): array
    {
        return $this->em->getRepository(Task::class)
            ->createQueryBuilder('t')
            ->andWhere('t.createdAt = :createdAt')
            ->setParameter('createdAt', $createdAt)
            ->getQuery()
            ->getResult();
    }
    public function getTasksByTitle(string $title): array
    {
        return $this->em->getRepository(Task::class)
            ->createQueryBuilder('t')
            ->andWhere('t.title = :title')
            ->setParameter('title', $title)
            ->getQuery()
            ->getResult();
    }
}