<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Repository\ClientRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/panel/copil/clients", name: "copil.clients.")]
class ClientController extends BaseController
{

    public function __construct(
        private ClientRepository $clientRepository,
    )
    {
    }

    #[Route("", name: "index")]
    public function index(): Response
    {
        return $this->render('admin/client/index.html.twig', [
            'menu' => 'copil.clients',
            'clients' => $this->clientRepository->findAll(),
        ]);
    }

}