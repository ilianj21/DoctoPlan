<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Service\TaskService;
use App\Service\TaskProviderInterface;
use App\Entity\Task;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use DateTimeImmutable;

class TaskServiceTest extends TestCase
{
    private TaskProviderInterface $provider;
    private EntityManagerInterface $em;
    private TaskService $service;

    protected function setUp(): void
    {
        $this->provider = $this->createMock(TaskProviderInterface::class);

        $this->service = new TaskService($this->provider);
    }

    public function testMarkAsDoneSuccess(): void
    {
        $project = new Project('Demo');
        $task = new Task('T1', $project);
        $task->setDueDate((new DateTimeImmutable())->modify('+1 day'));
        $task->setStatus(Task::STATUS_IN_PROGRESS);

        $this->provider->expects($this->once())->method('saveTask')->with($task);

        $this->service->markAsDone($task);
        $this->assertSame(Task::STATUS_DONE, $task->getStatus());
    }

    public function testMarkAsDoneThrowsWhenAlreadyDone(): void
    {
        $project = new Project('Demo');
        $task = new Task('T2', $project);
        $task->setStatus(Task::STATUS_DONE);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('La tâche est déjà terminée');

        $this->service->markAsDone($task);
    }

    public function testMarkAsDoneThrowsWhenExpired(): void
    {
        $project = new Project('Demo');
        $task = new Task('T3', $project);
        $task->setDueDate((new DateTimeImmutable())->modify('-10 days'));

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Impossible de terminer une tâche expirée depuis plus de 7 jours');

        $this->service->markAsDone($task);
    }

    public function testGetPrioritizedTasksSorting(): void
    {
        $projectId = 1;
        $t1 = new Task('A', new Project('P'));
        $t1->setDueDate((new DateTimeImmutable())->modify('+3 days'));
        $t1->setStatus(Task::STATUS_TODO);

        $t2 = new Task('B', new Project('P'));
        $t2->setDueDate((new DateTimeImmutable())->modify('+1 day'));
        $t2->setStatus(Task::STATUS_IN_PROGRESS);

        $stub = [$t1, $t2];
        $this->provider->method('getTasksByProject')->with($projectId)->willReturn($stub);

        $result = $this->service->getPrioritizedTasks($projectId);
        // t2 (sooner) should come first
        $this->assertSame([$t2, $t1], $result);
    }
}