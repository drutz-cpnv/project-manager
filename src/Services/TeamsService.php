<?php

namespace App\Services;

use App\Entity\TeamsRequest;
use App\Repository\MandateRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;

class TeamsService
{

    public const COMMAND_LIST_NON_DISPATCHED = "!nouveaux";
    public const COMMAND_LIST_DISPATCHED = "!distribué";
    public const COMMAND_LIST_PROJECTS = "!tous les projets";
    public const COMMAND_LIST_FINISHED_PROJECTS = "!projets terminé";

    public const COMMAND_LIST_PENDING_MANDATE = "!mandats en attentes";

    private const COMMAND_CONFIG = [
        self::COMMAND_LIST_NON_DISPATCHED => [
            'method' => "nonDispatched",
            'assert' => false
        ],
        self::COMMAND_LIST_DISPATCHED => [
            'method' => "dispatched",
            'assert' => false
        ],
        self::COMMAND_LIST_PROJECTS => [
            'method' => "allProjects",
            'assert' => false
        ],
        self::COMMAND_LIST_FINISHED_PROJECTS => [
            'method' => "allFinishedProjects",
            'assert' => false
        ],
    ];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private MandateRepository $mandateRepository,
        private ProjectRepository $projectRepository,
    )
    {}


    public function init(TeamsRequest $message)
    {
        $text = $message->getContent()['text'];

        $out = [
            'type' => "message",
            'text' => "Commande invalide !"
        ];

        if (!str_contains($text, '!')) return $out;

        foreach (self::COMMAND_CONFIG as $trigger => $config){
            if(!str_contains($text, $trigger)) continue;

            $out['text'] = $this->$config['method']();
        }

        return $out;
    }

    private function nonDispatched(): string
    {
        $out = "";
        foreach ($this->mandateRepository->findDireJePending() as $mandate) {
            $out .= "• ".$mandate->getTitle();
        }

        return $out;
    }

    private function dispatched(): string
    {
        $out = "";
        $mandates = $this->mandateRepository->findDireJeDispatched();
        if(empty($mandates)) return "Aucune donnée pour cette recherche.";
        foreach ($mandates as $mandate) {
            $out .= "• ".$mandate->getTitle();
        }

        return $out;
    }

    private function allProjects(): string
    {
        $out = "";
        $projects = $this->projectRepository->findAll();

        if(empty($projects)) return "Aucune donnée pour cette recherche.";

        foreach ($projects as $project) {
            $out .= "• ".$project->getTitle();
        }

        return $out;
    }
    private function allFinishedProjects(): string
    {
        $out = "";
        $projects = $this->projectRepository->findBy(['state' => 4]);

        if(empty($projects)) return "Aucune donnée pour cette recherche.";

        foreach ($projects as $project) {
            $out .= "• ".$project->getTitle();
        }

        return $out;
    }


}