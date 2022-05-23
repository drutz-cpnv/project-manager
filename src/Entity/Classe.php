<?php

namespace App\Entity;

use App\Repository\ClasseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClasseRepository::class)]
class Classe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $slug;

    #[ORM\OneToMany(mappedBy: 'class', targetEntity: Project::class)]
    private $projects;

    #[ORM\OneToMany(mappedBy: 'class', targetEntity: Person::class)]
    private $students;

    #[ORM\OneToOne(inversedBy: 'parent', targetEntity: self::class, cascade: ['persist', 'remove'])]
    private $children;

    #[ORM\OneToOne(mappedBy: 'children', targetEntity: self::class)]
    private $parent;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->students = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setClass($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getClass() === $this) {
                $project->setClass(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Person>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Person $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students[] = $student;
            $student->setClass($this);
        }

        return $this;
    }

    public function removeStudent(Person $student): self
    {
        if ($this->students->removeElement($student)) {
            // set the owning side to null (unless already changed)
            if ($student->getClass() === $this) {
                $student->setClass(null);
            }
        }

        return $this;
    }

    public function getAllStudents(): ArrayCollection
    {
        $output = new ArrayCollection();
        if(!is_null($this->getChildren())) {
            foreach ($this->getChildren()->getStudents() as $student) {
                $output->add($student);
            }
        }

        foreach ($this->getStudents() as $student) {
            $output->add($student);
        }

        return $output;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getChildren(): ?self
    {
        return $this->children;
    }

    public function setChildren(?self $children): self
    {
        $this->children = $children;

        return $this;
    }

    /**
     * @return Classe|null
     */
    public function getParent(): ?Classe
    {
        return $this->parent;
    }
}
