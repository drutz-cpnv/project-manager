<?php

namespace App\Entity;

use App\Repository\MandateRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MandateRepository::class)]
class Mandate
{

    public const STATE_PENDING_COPIL_CONFIRM = 1;
    public const STATE_PENDING_DIR = 2;
    public const STATE_ACTIVE = 3;
    public const STATE_REFUSED = 4;
    public const STATE_TERMINATED = 5;
    public const STATE_CANCEL = 6;

    private array $stateLabel = [
        1 => "En attente de validation par le COPIL",
        2 => "En attente de distribution par la Direction",
        3 => "Actif",
        4 => "Refusé",
        5 => "Terminé",
        6 => "Annulé",
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title = null;

    #[ORM\Column(type: 'text')]
    private $description = null;

    #[ORM\Column(type: 'date_immutable')]
    private $desiredDate = null;

    #[ORM\Column(type: 'integer')]
    private $uid = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt = null;

    #[ORM\ManyToOne(targetEntity: Client::class, cascade: ['persist'], inversedBy: 'mandates')]
    #[ORM\JoinColumn(nullable: false)]
    private $client;

    #[ORM\Column(type: 'boolean')]
    private $isDelegated = false;

    #[ORM\OneToMany(mappedBy: 'mandate', targetEntity: Project::class)]
    private $projects;

    #[ORM\Column(type: 'integer')]
    private $state = 1;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDesiredDate(): ?\DateTimeImmutable
    {
        return $this->desiredDate;
    }

    public function setDesiredDate(\DateTimeImmutable|DateTime $desiredDate): self
    {
        if($desiredDate instanceof DateTime) {
            $desiredDate = \DateTimeImmutable::createFromMutable($desiredDate);
        }
        $this->desiredDate = $desiredDate;

        return $this;
    }

    public function getUid(): ?int
    {
        return $this->uid;
    }

    public function setUid(int $uid): self
    {
        $this->uid = $uid;

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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getIsDelegated(): ?bool
    {
        return $this->isDelegated;
    }

    public function setIsDelegated(bool $isDelegated): self
    {
        $this->isDelegated = $isDelegated;

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
            $project->setMandate($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getMandate() === $this) {
                $project->setMandate(null);
            }
        }

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getStateLabel(): string
    {
        return $this->stateText($this->getState());
    }

    private function stateText(int $state): string
    {
        return $this->stateLabel[$state];
    }

}
