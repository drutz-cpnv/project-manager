<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Data\Settings;
use App\Form\AdminSettingsForm;
use App\Repository\SettingRepository;
use App\Services\SetupService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/admin/settings", name: "admin.settings.")]
class SettingsController extends BaseController
{

    public function __construct(
        private SettingRepository $settingRepository,
        private EntityManagerInterface $entityManager,
        private SetupService $setupService
    )
    {}

    #[Route("", name: "index")]
    public function index(Request $request): Response
    {
        $settings = (new Settings())->setSettings($this->settingRepository->findAll());

        $form = $this->createForm(AdminSettingsForm::class, $settings);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            return $this->redirectToRoute('admin.settings.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/settings/index.html.twig', [
            'menu' => "admin.settings",
            'form' => $form,
            'tab' => 'tab1',
        ]);
    }

    #[Route("/tools", name: "tools")]
    public function tools(Request $request): Response
    {
        return $this->render('admin/settings/tools.html.twig', [
            'menu' => "admin.settings",
            'tab' => 'tab2',
        ]);
    }

    #[Route("/updates", name: "updates")]
    public function updates(Request $request): Response
    {
        return $this->render('admin/settings/update.html.twig', [
            'menu' => "admin.settings",
            'tab' => 'tab3',
        ]);
    }


    #[Route("/tools/reset", name: "tools.reset", methods: ["POST"])]
    public function reset(Request $request): Response
    {
        if ($this->isCsrfTokenValid('reset'.$this->getUser()->getId(), $request->request->get('_token'))) {
            $this->setupService->reset();
        }
        return $this->redirectToRoute("admin.settings.index", [], Response::HTTP_SEE_OTHER);
    }

}