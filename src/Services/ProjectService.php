<?php

namespace App\Services;

use App\Entity\Note;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ProjectService
{

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Security $security,
        private readonly DefaultService $defaultService,
    )
    {
    }

    public function create(Project $project): void
    {
        $project->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable())
            ->setCreatedBy($this->security->getUser())
            ->setUpdatedBy($this->security->getUser())
            ->setClass($this->security->getUser()->getClass())
        ;

        $ms = $this->defaultService->getDefaultMilestones();

        foreach ($ms as $milestone) {
            $project->addMilestone($milestone);
        }

        $this->em->persist($project);
        $this->em->flush();
    }

    public function update(Project $project): void
    {
        $project->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();
    }

    public function addCoachFinalNotes(Project $project)
    {
        $project->addTeacherEvaluation((new Note())->setName("Cahier des charges"));
        $project->addTeacherEvaluation((new Note())->setName("Organigramme des tâches"));
        $project->addTeacherEvaluation((new Note())->setName("Planning"));
        $project->addTeacherEvaluation((new Note())->setName("Devis"));
        $project->addTeacherEvaluation((new Note())->setName("PV séances"));
        $project->addTeacherEvaluation((new Note())->setName("Classement des fichiers "));
        $project->addTeacherEvaluation((new Note())->setName("Clôture et archivages"));
    }

    public function delete(Project $project): void
    {
        $this->em->remove($project);
        $this->em->flush();
    }

}