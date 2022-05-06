<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Entity\Mandate;
use App\Form\AdminMandateFormType;
use App\Repository\MandateRepository;
use App\Services\MandateService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/admin/mandat", name: "admin.mandate.")]
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
        return $this->render('admin/mandate/index.html.twig', [
            'menu' => "admin.mandate",
            'mandates' => $this->mandateRepository->findAll(),
        ]);
    }

    #[Route("/new", name: "new")]
    public function new(Request $request): Response
    {
        $mandate = new Mandate();
        $form = $this->createForm(AdminMandateFormType::class, $mandate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->mandateService->manualCreate($mandate, $form);
            return $this->redirectToRoute('admin.mandate.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/mandate/new.html.twig', [
            'menu' => "admin.mandate",
            'form' => $form,
        ]);
    }



}