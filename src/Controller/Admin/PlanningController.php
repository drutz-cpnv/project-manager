<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Repository\ClasseRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/panel/copil/planning", name: "copil.planning.")]
class PlanningController extends BaseController
{

    public function __construct(
        private ClasseRepository $classeRepository
    )
    {
    }

    #[Route("", name: "index")]
    public function index(): Response
    {
        return $this->render('admin/planning/index.html.twig', [
            'menu' => 'copil.planning',
            'classes' => $this->classeRepository->findAll(),
        ]);
    }

}