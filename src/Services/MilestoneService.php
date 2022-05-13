<?php

namespace App\Services;

use App\Entity\Milestone;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class MilestoneService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private DefaultService $defaultService,
        private Security $security
    )
    {
    }

    public function initProject(Project $project) {
        $plus1Week = $project->getCreatedAt()->add(\DateInterval::createFromDateString("+1 week"));
        $defaultsMilestone = $this->defaultService->getDefaultMilestones();

        foreach ($defaultsMilestone as &$milestone) {
            $project->addMilestone($milestone);
        }

    }

    public function update(Milestone $milestone)
    {
        $milestone->setUpdatedBy($this->security->getUser());
        $milestone->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();
    }

    /**
     * @param Milestone|Milestone[] $milestones
     * @return void
     */
    public function finished(Milestone|array $milestones): void
    {
        if (is_array($milestones)) {
            foreach ($milestones as $milestone) {
                $milestone->setState(Milestone::STATE_DONE);
                $milestone->setFinished(true);
            }
        } else {
            $milestones->setState(Milestone::STATE_DONE);
            $milestones->setFinished(true);
        }

        $this->entityManager->flush();

    }

}