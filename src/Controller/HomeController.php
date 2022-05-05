<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: "app.")]
class HomeController extends BaseController
{
    
    #[Route("", name: "home")]
    public function index(): Response
    {
        return $this->render('home.html.twig', [
            'menu' => "home"
        ]);
    }

    #[Route("/placeholder", name: "placeholder")]
    public function placeholder(): Response
    {
        return $this->redirectToRoute("app.home", [], Response::HTTP_SEE_OTHER);
    }

}