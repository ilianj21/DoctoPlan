<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Service\RiskAnalyzerService;
use App\Service\TaskProviderInterface;
use App\Entity\Task;
use App\Entity\Project;
use DateTimeImmutable;

class RiskAnalyzerServiceTest extends TestCase
{
    private TaskProviderInterface $provider;
    private RiskAnalyzerService $service;

    protected function setUp(): void
    {
        $this->provider = $this->createMock(TaskProviderInterface::class);
        $this->service = new RiskAnalyzerService($this->provider);
    }

    public function testGetTasksAtRiskFiltersCorrectly(): void
    {
        $projectId = 2;
        $now = new DateTimeImmutable();
        $inRisk = new Task('Risk');
        $inRisk->setProject(new Project('P'));
        $inRisk->setDueDate($now->modify('+1 day')); $inRisk->setStatus(Task::STATUS_TODO);

        $outOfRisk = new Task('NoRisk');
        $outOfRisk->setProject(new Project('P'));
        $outOfRisk->setDueDate($now->modify('+5 days')); $outOfRisk->setStatus(Task::STATUS_TODO);

        /** @phpstan-ignore method.notFound */
        $this->provider->method('getTasksByProject')->with($projectId)->willReturn([$inRisk, $outOfRisk]);

        $result = $this->service->getTasksAtRisk($projectId);
        $this->assertCount(1, $result);
        $this->assertSame($inRisk, $result[0]);
    }
}