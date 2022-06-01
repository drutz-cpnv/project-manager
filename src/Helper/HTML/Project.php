<?php

namespace App\Helper\HTML;

use App\Data\Moment;

class Project
{

    private ?\App\Entity\Project $project = null;
    private ?int $length = null;
    private ?int $offset = null;

    public function __construct(\App\Entity\Project $project)
    {
        $this->project = $project;
    }

    public function setLength(Moment $moment, PlanningTable $planningTable): Project
    {
        $count = 1;
        $start = $moment->getStartAt();
        $end = $moment->getEndAt();

        $projectStart = $this->project->getCreatedAt()->modify("this ". $planningTable->getDisplayDay());

        $i = 1;
        while ($i <= 200) {
            $weekInterval = new \DateInterval('P'.$i.'W');
            $date = $projectStart->add($weekInterval);

            if($date >= $this->project->getSpecificationsEndDate()) break;

            $count++;
            $i++;

        }

        $this->length = $count;

        return $this;
    }

    public function setOffset(Moment $moment, PlanningTable $planningTable): Project
    {
        $count = -1;
        $start = $moment->getStartAt()->modify("this " . $planningTable->getDisplayDay());

        $i = 1;
        while ($i <= 200) {
            $weekInterval = new \DateInterval('P'.$i.'W');
            $date = $start->add($weekInterval);

            if($date >= $this->project->getCreatedAt()) break;

            $i++;
            $count++;
        }

        $this->offset = $count;

        return $this;
    }

    public function getProject(): \App\Entity\Project
    {
        return $this->project;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

}