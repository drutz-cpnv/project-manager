<?php

namespace App\Controller\Direction;

use App\Controller\BaseController;
use App\Entity\Classe;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/direje/effectif", name: "direje.resources.")]
class ResourcesController extends BaseController
{

    public function __construct(
        private UserRepository $userRepository,
    )
    {
    }

    #[Route("", name: "index")]
    public function index(): Response
    {
        /** @var Classe $class */
        $class = $this->getUser()->getClass();
        $users = $class->getStudents();
        return $this->render('direction/resources/index.html.twig', [
            'menu' => 'direje.resources',
            'resources' => $users
        ]);
    }
    
}