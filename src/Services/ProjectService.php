<?php

namespace App\Services;

use App\Entity\Project;
use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;

class ProjectService
{

    public function __construct(
        private readonly EntityManagerInterface $em
    )
    {
    }

    public function create(Project $project): void
    {
        $project->setCreatedAt(new \DateTimeImmutable());
        $project->setUpdatedAt(new \DateTimeImmutable());
        $this->em->persist($project);
        $this->em->flush();
    }

    public function update(Project $project): void
    {
        $project->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();
    }

}