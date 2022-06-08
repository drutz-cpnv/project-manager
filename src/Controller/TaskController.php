<?php

namespace App\Controller;

use App\Entity\Person;
use App\Entity\Project;
use App\Services\DefaultService;
use App\Services\ProjectService;
use App\Services\SetupService;
use App\Services\UpdateService;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/task", name: "task.")]
class TaskController extends BaseController
{

    public function __construct(
        private SetupService $setupService
    )
    {}

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

    #[Route("/update/{method}", name: "update")]
    public function updateItemOperation(string $method, UpdateService $updateService): Response
    {
        $updateService->$method();
        return $this->redirectToRoute('admin.settings.index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route("/user/create/{id}/copil", name: "user.create.copil", env: "dev")]
    public function createUserFromPerson(Person $person, UserService $userService): Response
    {
        $userService->testCreate($person, ['ROLE_COPIL']);
        return $this->redirectToRoute('app.home', [], Response::HTTP_SEE_OTHER);
    }

    #[Route("/reset", name: "reset")]
    public function reset()
    {
        $this->setupService->reset();
        return $this->redirectToRoute("app.home", [], Response::HTTP_SEE_OTHER);
    }


}