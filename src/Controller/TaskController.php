<?php

namespace App\Controller;

use App\Entity\Project;
use App\Services\DefaultService;
use App\Services\ProjectService;
use App\Services\UpdateService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/task", name: "task.")]
class TaskController extends BaseController
{

    #[Route("/project/{id}/delete", name: "project.delete", env: "dev")]
    public function deleteProject(Project $project, ProjectService $projectService): Response
    {
        $projectService->delete($project);
        return $this->redirectToRoute('app.home', [], Response::HTTP_SEE_OTHER);
    }

    #[Route("/default", name: "default_config")]
    public function loadDefault(DefaultService $defaultService): Response
    {
        $defaultService->all();
        return $this->redirectToRoute('app.home', [], Response::HTTP_SEE_OTHER);
    }

    #[Route("/update/{method}", name: "default_config")]
    public function updateItemOperation(string $method, UpdateService $updateService): Response
    {
        $updateService->$method();
        return $this->redirectToRoute('app.home', [], Response::HTTP_SEE_OTHER);
    }

}