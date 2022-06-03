<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Helper\HTML\PlanningTable;
use App\Repository\ClasseRepository;
use App\Repository\SettingRepository;
use App\Services\Intranet\IntranetClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/panel/copil/planning", name: "copil.planning.")]
class PlanningController extends BaseController
{

    public function __construct(
        private ClasseRepository $classeRepository,
        private IntranetClient $intranetClient,
        private SettingRepository $settingRepository,
    )
    {
    }

    #[Route("", name: "index")]
    public function index(): Response
    {
        $moment = $this->intranetClient->findCurrentMoment();
        $day = $this->settingRepository->findOneBy(['settingKey' => "planning.display_day"]);

        $tables = [];

        foreach($this->classeRepository->findAll() as $class) {
            $tables[] = (new PlanningTable($day->getValue()))
                ->setMoment($moment)
                ->setClass($class)
                ->setDays()
                ->setStudents()
            ;
        }

        return $this->render('admin/planning/index.html.twig', [
            'menu' => 'copil.planning',
            'classes' => $this->classeRepository->findAll(),
            'tables' => $tables,
        ]);
    }

}