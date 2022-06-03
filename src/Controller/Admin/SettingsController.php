<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Data\Settings;
use App\Form\AdminSettingsForm;
use App\Repository\SettingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/admin/settings", name: "admin.settings.")]
class SettingsController extends BaseController
{

    public function __construct(
        private SettingRepository $settingRepository,
    )
    {}

    #[Route("", name: "index")]
    public function index(Request $request): Response
    {

        $settings = (new Settings())->setSettings($this->settingRepository->findAll());

        $form = $this->createForm(AdminSettingsForm::class, $settings);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            dd($settings);
        }

        return $this->renderForm('admin/settings/index.html.twig', [
            'menu' => "admin.settings",
            'form' => $form
        ]);
    }

}