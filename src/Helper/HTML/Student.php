<?php

namespace App\Helper\HTML;

use App\Data\Moment;
use App\Entity\Person;
use Doctrine\Common\Collections\ArrayCollection;
use phpDocumentor\Reflection\Types\This;

class Student
{

    private ?Person $student = null;
    private ?array $projects = null;

    public function setStudent(Person $student): Student
    {
        $this->student = $student;
        return $this;
    }

    public function setProjects($projects): Student
    {
        $this->projects = $projects;
        return $this;
    }

    /**
     * @return Person|null
     */
    public function getStudent(): ?Person
    {
        return $this->student;
    }

    /**
     * @return Project[]
     */
    public function getProjects(): array
    {
        return $this->projects;
    }



}