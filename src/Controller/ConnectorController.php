<?php

namespace App\Controller;

use App\Entity\TeamsRequest;
use App\Services\TeamsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/connector", name: "connector.")]
class ConnectorController extends BaseController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private TeamsService $teamsService,
    )
    {
    }

    #[Route("/teams", name: "teams")]
    public function teams(Request $request): Response
    {
        $teamsRequest = (new TeamsRequest())
            ->setContent([
                'content' => $request->getContent()
            ])
            ->setCreatedAt(new \DateTimeImmutable())
        ;

        $this->entityManager->persist($teamsRequest);
        $this->entityManager->flush();

        return $this->json($this->teamsService->init($teamsRequest));
    }

}