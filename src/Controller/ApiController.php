<?php

namespace App\Controller;

use App\Entity\Mandate;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Storage\StorageInterface;

#[Route("/api", name: "api.")]
class ApiController extends BaseController
{

    public function __construct(
        private StorageInterface $storage
    )
    {}

    #[Route("/mandate/{id}", name: "mandate.show")]
    public function findOneMandate(Mandate $mandate)
    {
        if($mandate->getProjects()->count() > 0) {
            $pj = $mandate->getProjects()->first();
            $project = [
                'manager' => [
                    'fullname' => $pj->getManager()->getUser()->getFullname(),
                    'email' => $pj->getManager()->getUser()->getEmail(),
                ],
                'validatedDate' => $pj->getSpecificationsEndDate()->format("d.m.Y"),
                'startedAt' => $pj->getCreatedAt()->format("d.m.Y H:i"),
                'state' => $pj->getStatus()
            ];
        }
        else {
            $project = [
                'none' => true,
            ];
        }

        $files = [];

        foreach ($mandate->getFiles() as $file) {
            $files[] = [
                'uri' => $this->storage->resolveUri($file),
                'filename' => $file->getTitle()
            ];
        }

        $out = [
            'title' => $mandate->getTitle(),
            'description' => $mandate->getDescription(),
            'desiredDate' => $mandate->getDesiredDate()->format("d.m.Y"),
            'state' => $mandate->getStateLabel(),
            'dispatch' => $mandate->getProjects()->count() > 0,
            'project' => $project,
            'createdAt' => $mandate->getCreatedAt()->format("d.m.Y H:i"),
            'files' => $files
        ];

        return $this->json($out);
    }

}