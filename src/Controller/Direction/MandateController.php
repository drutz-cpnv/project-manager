<?php

namespace App\Controller\Direction;

use App\Controller\BaseController;
use App\Entity\Mandate;
use App\Form\AdminMandateFormType;
use App\Repository\MandateRepository;
use App\Services\MandateService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/panel/direje/mandats", name: "direje.mandate.")]
class MandateController extends BaseController
{

    public function __construct(
        private MandateService $mandateService,
        private MandateRepository $mandateRepository,
    )
    {
    }

    #[Route("", name: "index")]
    public function index(): Response
    {
        return $this->render('direction/mandate/index.html.twig', [
            'menu' => "direje.mandate",
            'mandates' => $this->mandateRepository->findForDireJe(),
        ]);
    }

    #[Route("/{id}", name: "show")]
    public function show(Mandate $mandate): Response
    {
        return $this->render('direction/mandate/show.html.twig', [
            'menu' => 'direje.mandate',
            'mandate' => $mandate
        ]);
    }

    #[Route("/{id}/accept", name: "accept", methods: ["POST"])]
    public function direjeAccept(Mandate $mandate, Request $request): Response
    {
        if ($this->isCsrfTokenValid('direjeAccept'.$mandate->getId(), $request->request->get('_token'))) {
            $this->mandateService->acceptDIR($mandate);
        }
        return $this->redirectToRoute('direje.mandate.show', ['id' => $mandate->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route("/{id}/refuse", name: "refuse", methods: ["POST"])]
    public function direjeRefuse(Mandate $mandate, Request $request): Response
    {
        if ($this->isCsrfTokenValid('direjeRefuse'.$mandate->getId(), $request->request->get('_token'))) {
            $this->mandateService->refuse($mandate);
        }
        return $this->redirectToRoute('direje.mandate.show', ['id' => $mandate->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route("/{id}/change-state/{stateId}", name: "state_change", methods: ["POST"])]
    public function changeState(Mandate $mandate, string $stateId, Request $request): Response
    {
        if ($this->isCsrfTokenValid('direjeRefuse'.$mandate->getId(), $request->request->get('_token'))) {
            $this->mandateService->refuse($mandate);
        }
        return $this->redirectToRoute('direje.mandate.show', ['id' => $mandate->getId()], Response::HTTP_SEE_OTHER);
    }

}