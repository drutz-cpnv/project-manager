<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use App\Services\UpdateService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/panel", name: "panel.")]
class AppController extends BaseController
{

    #[Route("", name: "home")]
    public function index(UpdateService $updateService): Response
    {
        return $this->render('home.html.twig', [
            'menu' => "home"
        ]);
    }

    #[Route("/mes-projets", name: "my_projects")]
    public function myProjects(): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");

        return $this->render('pages/my_projects.html.twig', [
            'menu' => 'my-projects',
        ]);
    }

}