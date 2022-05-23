<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $firstname;

    #[ORM\Column(type: 'string', length: 255)]
    private $lastname;

    #[ORM\Column(type: 'string', length: 255)]
    private $email;

    #[ORM\OneToOne(mappedBy: 'person', targetEntity: User::class, cascade: ['persist', 'remove'])]
    private $user;

    #[ORM\ManyToOne(targetEntity: PersonType::class, inversedBy: 'people')]
    private $type;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private $updatedAt;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $externalId;

    #[ORM\OneToMany(mappedBy: 'coach', targetEntity: Project::class)]
    private $supervisedProjects;

    #[ORM\ManyToOne(targetEntity: Classe::class, inversedBy: 'students')]
    private $class;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $canCoach = false;

    #[ORM\OneToOne(mappedBy: 'person', targetEntity: Client::class, cascade: ['persist', 'remove'])]
    private $client;

    #[ORM\OneToMany(mappedBy: 'coach', targetEntity: PersonalEvaluation::class)]
    private $studentEvaluations;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTimeImmutable());
        $this->setUpdatedAt(new \DateTimeImmutable());
        $this->supervisedProjects = new ArrayCollection();
        $this->studentEvaluations = new ArrayCollection();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function onPreUpdate(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return strtoupper($this->lastname);
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setPerson(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getPerson() !== $this) {
            $user->setPerson($this);
        }

        $this->user = $user;

        return $this;
    }

    public function getType(): ?PersonType
    {
        return $this->type;
    }

    public function isStudent(): bool
    {
        return $this->getType()->getSlug() === 'student';
    }

    public function isTeacher(): bool
    {
        return $this->getType()->getSlug() === 'teacher';
    }

    public function isClient(): bool
    {
        return $this->getType()->getSlug() === 'client';
    }

    public function isDirector(): bool
    {
        return in_array('ROLE_DIRECTOR', $this->getUser()->getRoles());
    }

    public function setType(?PersonType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getExternalId(): ?int
    {
        return $this->externalId;
    }

    public function setExternalId(?int $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getSupervisedProjects(): Collection
    {
        return $this->supervisedProjects;
    }

    public function addSupervisedProject(Project $supervisedProject): self
    {
        if (!$this->supervisedProjects->contains($supervisedProject)) {
            $this->supervisedProjects[] = $supervisedProject;
            $supervisedProject->setCoach($this);
        }

        return $this;
    }

    public function removeSupervisedProject(Project $supervisedProject): self
    {
        if ($this->supervisedProjects->removeElement($supervisedProject)) {
            // set the owning side to null (unless already changed)
            if ($supervisedProject->getCoach() === $this) {
                $supervisedProject->setCoach(null);
            }
        }

        return $this;
    }

    public function getClass(): ?Classe
    {
        return $this->class;
    }

    public function setClass(?Classe $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getLastname() . " " . $this->getFirstname();
    }

    public function getCanCoach(): ?bool
    {
        return $this->canCoach;
    }

    public function setCanCoach(?bool $canCoach): self
    {
        $this->canCoach = $canCoach;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        // unset the owning side of the relation if necessary
        if ($client === null && $this->client !== null) {
            $this->client->setPerson(null);
        }

        // set the owning side of the relation if necessary
        if ($client !== null && $client->getPerson() !== $this) {
            $client->setPerson($this);
        }

        $this->client = $client;

        return $this;
    }

    public function getMainClass(): ?Classe
    {
        if(is_null($this->getClass())) return null;
        if(is_null($this->getClass()->getParent())) return $this->getClass();
        return $this->getClass()->getParent();
        //return $this->getClass()->getChildren() ?: $this->getClass();
    }

    /**
     * @return Collection<int, PersonalEvaluation>
     */
    public function getStudentEvaluations(): Collection
    {
        return $this->studentEvaluations;
    }

    public function addStudentEvaluation(PersonalEvaluation $studentEvaluation): self
    {
        if (!$this->studentEvaluations->contains($studentEvaluation)) {
            $this->studentEvaluations[] = $studentEvaluation;
            $studentEvaluation->setCoach($this);
        }

        return $this;
    }

    public function removeStudentEvaluation(PersonalEvaluation $studentEvaluation): self
    {
        if ($this->studentEvaluations->removeElement($studentEvaluation)) {
            // set the owning side to null (unless already changed)
            if ($studentEvaluation->getCoach() === $this) {
                $studentEvaluation->setCoach(null);
            }
        }

        return $this;
    }


}
