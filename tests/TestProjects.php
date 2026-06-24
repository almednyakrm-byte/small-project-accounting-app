<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\ProjectsController;
use App\Repository\ProjectsRepository;
use App\Entity\Project;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class TestProjects extends TestCase
{
    private $projectsController;
    private $projectsRepository;
    private $router;
    private $pdo;

    protected function setUp(): void
    {
        $this->projectsRepository = $this->createMock(ProjectsRepository::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->pdo = $this->createMock(\PDO::class);

        $this->projectsController = new ProjectsController($this->projectsRepository, $this->router, $this->pdo);
    }

    public function testGetProjects()
    {
        $expectedResponse = new JsonResponse(['projects' => []]);
        $this->projectsRepository->expects($this->once())
            ->method('getAllProjects')
            ->willReturn([]);

        $response = $this->projectsController->getProjects();
        $this->assertEquals($expectedResponse, $response);
    }

    public function testGetProjectById()
    {
        $projectId = 1;
        $expectedResponse = new JsonResponse(['project' => new Project()]);
        $this->projectsRepository->expects($this->once())
            ->method('getProjectById')
            ->with($projectId)
            ->willReturn(new Project());

        $response = $this->projectsController->getProjectById($projectId);
        $this->assertEquals($expectedResponse, $response);
    }

    public function testCreateProject()
    {
        $project = new Project();
        $expectedResponse = new JsonResponse(['project' => $project]);
        $this->projectsRepository->expects($this->once())
            ->method('createProject')
            ->with($project)
            ->willReturn($project);

        $request = new Request([], [], ['project' => $project]);
        $response = $this->projectsController->createProject($request);
        $this->assertEquals($expectedResponse, $response);
    }

    public function testUpdateProject()
    {
        $projectId = 1;
        $project = new Project();
        $expectedResponse = new JsonResponse(['project' => $project]);
        $this->projectsRepository->expects($this->once())
            ->method('updateProject')
            ->with($projectId, $project)
            ->willReturn($project);

        $request = new Request([], [], ['project' => $project]);
        $response = $this->projectsController->updateProject($projectId, $request);
        $this->assertEquals($expectedResponse, $response);
    }

    public function testDeleteProject()
    {
        $projectId = 1;
        $expectedResponse = new JsonResponse(['message' => 'Project deleted successfully']);
        $this->projectsRepository->expects($this->once())
            ->method('deleteProject')
            ->with($projectId)
            ->willReturn(true);

        $response = $this->projectsController->deleteProject($projectId);
        $this->assertEquals($expectedResponse, $response);
    }
}



// ProjectsController.php

namespace App\Controller;

use App\Repository\ProjectsRepository;
use App\Entity\Project;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProjectsController
{
    private $projectsRepository;
    private $router;
    private $pdo;

    public function __construct(ProjectsRepository $projectsRepository, RouterInterface $router, \PDO $pdo)
    {
        $this->projectsRepository = $projectsRepository;
        $this->router = $router;
        $this->pdo = $pdo;
    }

    public function getProjects()
    {
        $projects = $this->projectsRepository->getAllProjects();
        return new JsonResponse(['projects' => $projects]);
    }

    public function getProjectById($projectId)
    {
        $project = $this->projectsRepository->getProjectById($projectId);
        return new JsonResponse(['project' => $project]);
    }

    public function createProject(Request $request)
    {
        $project = new Project();
        $project->fromArray($request->request->all());
        $project = $this->projectsRepository->createProject($project);
        return new JsonResponse(['project' => $project]);
    }

    public function updateProject($projectId, Request $request)
    {
        $project = new Project();
        $project->fromArray($request->request->all());
        $project = $this->projectsRepository->updateProject($projectId, $project);
        return new JsonResponse(['project' => $project]);
    }

    public function deleteProject($projectId)
    {
        $this->projectsRepository->deleteProject($projectId);
        return new JsonResponse(['message' => 'Project deleted successfully']);
    }
}