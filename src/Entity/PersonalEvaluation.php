<?php

namespace App\Entity;

use App\Repository\PersonalEvaluationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonalEvaluationRepository::class)]
class PersonalEvaluation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Member::class, inversedBy: 'evaluations')]
    #[ORM\JoinColumn(nullable: false)]
    private $student;

    #[ORM\ManyToOne(targetEntity: Person::class, inversedBy: 'studentEvaluations')]
    #[ORM\JoinColumn(nullable: false)]
    private $coach;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $updatedAt;

    #[ORM\OneToMany(mappedBy: 'studentEvaluation', targetEntity: Note::class, cascade: ["persist"], orphanRemoval: true)]
    private $notes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrject(): ?string
    {
        return $this->prject;
    }

    public function setPrject(string $prject): self
    {
        $this->prject = $prject;

        return $this;
    }

    public function getStudent(): ?Member
    {
        return $this->student;
    }

    public function setStudent(?Member $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getCoach(): ?Person
    {
        return $this->coach;
    }

    public function setCoach(?Person $coach): self
    {
        $this->coach = $coach;

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

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setStudentEvaluation($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getStudentEvaluation() === $this) {
                $note->setStudentEvaluation(null);
            }
        }

        return $this;
    }
}
