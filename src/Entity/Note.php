<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
class Note
{
    public const COMMUNICATION = 1;
    public const ATTITUDE = 2;
    public const TIME_LIMIT = 3;
    public const PROACTIVITY = 4;
    public const SPECIFICATIONS = 5;
    public const TASKS_LIST = 6;
    public const PLANNING = 7;
    public const ESTIMATE = 8;
    public const VERBAL_TRIAL = 9;
    public const FILE_HIERARCHY = 10;
    public const ARCHIVING = 11;

    private array $typesLabel = [
        self::COMMUNICATION => "Communiquation",
        self::ATTITUDE => "Attitude professionnelle",
        self::TIME_LIMIT => "Délais",
        self::PROACTIVITY => "Proactivité",
        self::SPECIFICATIONS => "Cahier des charges",
        self::TASKS_LIST => "Liste des tâches",
        self::PLANNING => "Plannification",
        self::ESTIMATE => "Devis",
        self::VERBAL_TRIAL => "PV Séances",
        self::FILE_HIERARCHY => "Hiérarchie des fichiers",
        self::ARCHIVING => "Clôture et archivage",
    ];


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank()]
    #[Assert\LessThanOrEqual(6)]
    #[Assert\GreaterThanOrEqual(1)]
    private $value;

    #[ORM\Column(type: 'text', nullable: true)]
    private $comment;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: PersonalEvaluation::class, inversedBy: 'notes')]
    #[ORM\JoinColumn(nullable: false)]
    private $studentEvaluation;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

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

    public function getStudentEvaluation(): ?PersonalEvaluation
    {
        return $this->studentEvaluation;
    }

    public function setStudentEvaluation(?PersonalEvaluation $studentEvaluation): self
    {
        $this->studentEvaluation = $studentEvaluation;

        return $this;
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
}
