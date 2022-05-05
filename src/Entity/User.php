<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this username')]
#[ORM\HasLifecycleCallbacks()]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\Email()]
    private $email;

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\OneToOne(inversedBy: 'user', targetEntity: Person::class, cascade: ['persist', 'remove'])]
    private $person;

    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users')]
    private $roles;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: self::class)]
    private $updatedBy;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Project::class)]
    private $createdProjects;

    #[ORM\OneToMany(mappedBy: 'updatedBy', targetEntity: Project::class)]
    private $updatedProjects;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Member::class)]
    private $memberships;

    #[ORM\OneToMany(mappedBy: 'coach', targetEntity: Project::class)]
    private $supervisedProjects;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTimeImmutable());
        $this->setUpdatedAt(new \DateTimeImmutable());
        $this->roles = new ArrayCollection();
        $this->createdProjects = new ArrayCollection();
        $this->updatedProjects = new ArrayCollection();
        $this->memberships = new ArrayCollection();
        $this->supervisedProjects = new ArrayCollection();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function onPreUpdate()
    {
        $this->setUpdatedAt(new \DateTimeImmutable());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail($email)
    {
       $this->email = $email;
       return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->getPerson()->getFirstname();
    }

    public function getLastname(): ?string
    {
        return $this->getPerson()->getLastname();
    }

    public function getFullname(): string
    {
        return $this->getFirstname() . " " . $this->getLastname();
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = [];
        foreach ($this->roles as $role) {
            $roles[] = $role->getSlug();
        }
        return $roles;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        $this->roles->removeElement($role);

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

    public function getUpdatedBy(): ?self
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?self $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getFullname();
    }

    /**
     * @return Collection<int, Project>
     */
    public function getCreatedProjects(): Collection
    {
        return $this->createdProjects;
    }

    public function addCreatedProject(Project $project): self
    {
        if (!$this->createdProjects->contains($project)) {
            $this->createdProjects[] = $project;
            $project->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCreatedProject(Project $project): self
    {
        if ($this->createdProjects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getCreatedBy() === $this) {
                $project->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getUpdatedProjects(): Collection
    {
        return $this->updatedProjects;
    }

    public function addUpdatedProject(Project $updatedProject): self
    {
        if (!$this->updatedProjects->contains($updatedProject)) {
            $this->updatedProjects[] = $updatedProject;
            $updatedProject->setUpdatedBy($this);
        }

        return $this;
    }

    public function removeUpdatedProject(Project $updatedProject): self
    {
        if ($this->updatedProjects->removeElement($updatedProject)) {
            // set the owning side to null (unless already changed)
            if ($updatedProject->getUpdatedBy() === $this) {
                $updatedProject->setUpdatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Member>
     */
    public function getMemberships(): Collection
    {
        return $this->memberships;
    }

    public function addMembership(Member $membership): self
    {
        if (!$this->memberships->contains($membership)) {
            $this->memberships[] = $membership;
            $membership->setUser($this);
        }

        return $this;
    }

    public function removeMembership(Member $membership): self
    {
        if ($this->memberships->removeElement($membership)) {
            // set the owning side to null (unless already changed)
            if ($membership->getUser() === $this) {
                $membership->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Project[]
     */
    public function getProjects(): array
    {
        $projects = [];

        foreach ($this->getMemberships() as $membership) {
            $projects[] = $membership->getProject();
        }

        return $projects;
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
}
