<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/admin/projects", name: "admin.project.")]
class ProjectController extends AbstractController
{

    #[Route("", name: "index")]
    public function index(): Response
    {
        return $this->render('admin/projects/index.html.twig', [
            'menu' => 'admin.project'
        ]);
    }



}