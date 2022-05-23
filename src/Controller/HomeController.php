<?php

namespace App\Controller;

use App\Repository\PersonRepository;
use App\Repository\UserRepository;
use App\Services\UpdateService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("", name: "app.")]
class HomeController extends BaseController
{

    public function __construct(
        private PersonRepository $personRepository,
        private EntityManagerInterface $em,
    )
    {
    }

    #[Route("", name: "home")]
    public function index(UpdateService $updateService): Response
    {
        return $this->render('front/pages/home.html.twig', [
            'menu' => "home"
        ]);
    }

    #[Route("/placeholder", name: "placeholder")]
    public function placeholder(): Response
    {
        return $this->redirectToRoute("app.home", [], Response::HTTP_SEE_OTHER);
    }

}