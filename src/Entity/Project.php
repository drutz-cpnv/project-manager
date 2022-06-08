<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{

    public const STATE_IN_PROGRESS = 1;
    public const STATE_STANDBY = 2;
    public const STATE_CANCELED = 3;
    public const STATE_FINISHED = 4;

    public const MANDATE_RELATED_STATE = [
        self::STATE_IN_PROGRESS => Mandate::STATE_ACTIVE,
        self::STATE_STANDBY => Mandate::STATE_ACTIVE,
        self::STATE_CANCELED => Mandate::STATE_CANCEL,
        self::STATE_FINISHED => Mandate::STATE_TERMINATED,
    ];

    public const STATE_ICONS = [
        self::STATE_IN_PROGRESS => 'flash',
        self::STATE_STANDBY => 'pause-circle',
        self::STATE_CANCELED => 'x-octagon',
        self::STATE_FINISHED => 'check-circle'
    ];

    public const STATE_COLOR = [
        self::STATE_IN_PROGRESS => 'text-warning',
        self::STATE_STANDBY => 'text-secondary',
        self::STATE_CANCELED => 'text-muted',
        self::STATE_FINISHED => 'text-primary'
    ];

    public const STATE_LABEL = [
        self::STATE_IN_PROGRESS => "En cours",
        self::STATE_STANDBY => "En pause",
        self::STATE_CANCELED => "Annulé",
        self::STATE_FINISHED => "Terminé",
     ];

    public const STATE_SLUG = [
        self::STATE_IN_PROGRESS => "in-progress",
        self::STATE_STANDBY => "pause",
        self::STATE_CANCELED => "cancel",
        self::STATE_FINISHED => "finished",
    ];


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'createdProjects')]
    private $createdBy;

    #[ORM\Column(type: 'datetime_immutable')]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'updatedProjects')]
    private $updatedBy;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Milestone::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private $milestones;

    #[ORM\ManyToOne(targetEntity: Mandate::class, inversedBy: 'projects')]
    private $mandate;

    #[ORM\ManyToOne(targetEntity: Person::class, inversedBy: 'supervisedProjects')]
    private $coach;

    #[ORM\Column(type: 'date', nullable: true)]
    private $specificationsEndDate = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $state = self::STATE_IN_PROGRESS;

    #[ORM\ManyToOne(targetEntity: Classe::class, inversedBy: 'projects')]
    private $class;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Member::class, orphanRemoval: true)]
    private $members;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: File::class)]
    private $files;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Note::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private $teacherEvaluation;

    #[ORM\OneToOne(mappedBy: 'project', targetEntity: ClientEvaluation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private $clientEvaluation;

    #[ORM\Column(type: 'integer')]
    private $code;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTimeImmutable());
        $this->setUpdatedAt(new \DateTimeImmutable());
        $this->milestones = new ArrayCollection();
        $this->members = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->teacherEvaluation = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus()
    {
        return self::STATE_LABEL[$this->getState()];
    }

    public function getUid(): string
    {
        return $this->getMandate()->getUid();
    }

    public function getClient()
    {
        return $this->getMandate()->getClient();
    }

    public function getDescription()
    {
        return $this->getMandate()->getDescription();
    }

    public function getTitle()
    {
        return $this->getMandate()->getTitle();
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

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User|UserInterface $createdBy): self
    {
        $this->createdBy = $createdBy;

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

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(User|UserInterface $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * @return Collection<int, Milestone>
     */
    public function getMilestones(): Collection
    {
        return $this->milestones;
    }

    public function addMilestone(Milestone $milestone): self
    {
        if (!$this->milestones->contains($milestone)) {
            $this->milestones[] = $milestone;
            $milestone->setProject($this);
        }

        return $this;
    }

    public function removeMilestone(Milestone $milestone): self
    {
        if ($this->milestones->removeElement($milestone)) {
            // set the owning side to null (unless already changed)
            if ($milestone->getProject() === $this) {
                $milestone->setProject(null);
            }
        }

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

    public function getMandate(): ?Mandate
    {
        return $this->mandate;
    }

    public function setMandate(?Mandate $mandate): self
    {
        $this->mandate = $mandate;

        return $this;
    }

    public function getSpecificationsEndDate(): ?\DateTimeInterface
    {
        return $this->specificationsEndDate;
    }

    public function setSpecificationsEndDate(\DateTimeInterface $specificationsEndDate): self
    {
        $this->specificationsEndDate = $specificationsEndDate;

        return $this;
    }


    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(?int $state): self
    {
        $this->state = $state;

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

    public function userIsMember(User $user): bool
    {
        $o = false;
        foreach ($this->getMembers() as $member) {
            if (!$o && $member->getUser() === $user) {
                $o = true;
            }
        }
        return $o;
    }

    public function getManager(): ?Member
    {
        foreach ($this->getMembers() as $member) {
            if($member->getIsProjectManager()) return $member;
        }
        return null;
    }

    /**
     * @return Collection<int, Member>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(Member $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
            $member->setProject($this);
        }

        return $this;
    }

    public function removeMember(Member $member): self
    {
        if ($this->members->removeElement($member)) {
            // set the owning side to null (unless already changed)
            if ($member->getProject() === $this) {
                $member->setProject(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }

    /**
     * @return Collection<int, File>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setProject($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getProject() === $this) {
                $file->setProject(null);
            }
        }

        return $this;
    }

    public function getDuration()
    {
        if(is_null($this->getSpecificationsEndDate())) return null;
        return $this->getSpecificationsEndDate()->diff($this->getCreatedAt());
    }

    public function getOffset()
    {
        $startY = new \DateTime("2021-08-01");
        return $this->getCreatedAt()->diff($startY);
    }

    public static function STATES()
    {
        $out = [];
        foreach (self::STATE_SLUG as $key => $value) {
            $out[$key] = [
                'slug' => $value,
                'label' => self::STATE_LABEL[$key],
                'color' => self::STATE_COLOR[$key],
                'icon' => self::STATE_ICONS[$key]
            ];
        }
        return $out;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getTeacherEvaluation(): Collection
    {
        return $this->teacherEvaluation;
    }

    public function addTeacherEvaluation(Note $teacherEvaluation): self
    {
        if (!$this->teacherEvaluation->contains($teacherEvaluation)) {
            $this->teacherEvaluation[] = $teacherEvaluation;
            $teacherEvaluation->setProject($this);
        }

        return $this;
    }

    public function removeTeacherEvaluation(Note $teacherEvaluation): self
    {
        if ($this->teacherEvaluation->removeElement($teacherEvaluation)) {
            // set the owning side to null (unless already changed)
            if ($teacherEvaluation->getProject() === $this) {
                $teacherEvaluation->setProject(null);
            }
        }

        return $this;
    }

    public function getClientEvaluation(): ?ClientEvaluation
    {
        return $this->clientEvaluation;
    }

    public function setClientEvaluation(ClientEvaluation $clientEvaluation): self
    {
        // set the owning side of the relation if necessary
        if ($clientEvaluation->getProject() !== $this) {
            $clientEvaluation->setProject($this);
        }

        $this->clientEvaluation = $clientEvaluation;

        return $this;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

}
