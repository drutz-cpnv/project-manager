<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends BaseController
{

    #[Route("/panel/mes-projets", name: "app.my_projects")]
    public function myProjects(): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");

        return $this->render('pages/my_projects.html.twig', [
            'menu' => 'my-projects',
        ]);
    }

}