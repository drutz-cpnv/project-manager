<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Entity\Client;
use App\Form\ClientType;
use App\Form\CompleteClientFormType;
use App\Repository\ClientRepository;
use App\Services\ClientService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/panel/copil/clients", name: "copil.clients.")]
class ClientController extends BaseController
{

    public function __construct(
        private ClientRepository $clientRepository,
        private ClientService $clientService,
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

    #[Route("/{id}", name: "show")]
    public function show(Client $client): Response
    {
        return $this->render('admin/client/show.html.twig', [
            'menu' => 'copil.clients',
            'client' => $client,
            'tab' => "tab1"
        ]);
    }

    #[Route("/{id}/mandates", name: "show.mandates")]
    public function showMandates(Client $client): Response
    {
        return $this->render('admin/client/show_mandates.html.twig', [
            'menu' => 'copil.clients',
            'client' => $client,
            'tab' => "tab2",
        ]);
    }

    #[Route("/{id}/edit", name: "edit")]
    public function edit(Client $client, Request $request): Response
    {
        $form = $this->createForm(CompleteClientFormType::class, $client);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->clientService->update($client);
            return $this->redirectToRoute('copil.clients.show', ['id' => $client->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/client/edit.html.twig', [
            'menu' => 'copil.clients',
            'client' => $client,
            'tab' => "tab3",
            'form' => $form->createView()
        ]);
    }

}