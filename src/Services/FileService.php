<?php

namespace App\Services;

use App\Entity\File;
use App\Repository\MandateRepository;
use Doctrine\ORM\EntityManagerInterface;

class FileService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private MandateRepository $mandateRepository,
        private ProjectService $projectService,
    )
    {
    }

    public function create(File $file)
    {
        $file->setCreatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($file);
        $this->entityManager->flush();
    }

}