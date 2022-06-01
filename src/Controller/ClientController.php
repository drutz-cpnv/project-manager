<?php

namespace App\Controller;

use App\Entity\Mandate;
use App\Entity\User;
use App\Form\MandateType;
use App\Repository\ClientRepository;
use App\Services\ClientService;
use App\Services\MandateService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("", name: "app.client.")]
class ClientController extends BaseController
{

    public function __construct(
        private ClientRepository $clientRepository,
        private ClientService $clientService,
        private MandateService $mandateService
    )
    {}

    #[Route("/nouveau-mandat", name: "new_mandate")]
    public function newMandate(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $client = $user->getPerson()->getClient();

        if(is_null($client)) {
            $client = $this->clientService->createFromUser($user);
        }

        $mandate = (new Mandate())
            ->setClient($client)
        ;

        $form = $this->createForm(MandateType::class, $mandate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->mandateService->create($mandate);
            return $this->redirectToRoute('app.home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('front/pages/mandate_form.html.twig', [
            'form' => $form
        ]);
    }

    #[Route("/mes-mandats", name: "index")]
    public function myMandates(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $mandates = $user->getPerson()->getClient()->getMandates();
        return $this->render('front/pages/mandate/index.html.twig', [
            'mandates' => $mandates,
        ]);
    }





}