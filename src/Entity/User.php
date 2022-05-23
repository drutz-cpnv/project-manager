<?php

namespace App\Entity;

use App\Helper\StringFormat;
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

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: File::class)]
    private $files;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Document::class)]
    private $documents;


    public function __construct()
    {
        $this->setCreatedAt(new \DateTimeImmutable());
        $this->setUpdatedAt(new \DateTimeImmutable());
        $this->roles = new ArrayCollection();
        $this->createdProjects = new ArrayCollection();
        $this->updatedProjects = new ArrayCollection();
        $this->memberships = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->documents = new ArrayCollection();
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
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
        $roles[] = "ROLE_USER";
         foreach ($this->roles as $role) {
            $roles[] = $role->getSlug();
        }
        return $roles;
    }

    public function getRoleString()
    {
        return StringFormat::CSV($this->getRoles());
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
            $file->setCreatedBy($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getCreatedBy() === $this) {
                $file->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setCreatedBy($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getCreatedBy() === $this) {
                $document->setCreatedBy(null);
            }
        }

        return $this;
    }


    /**
     * Data from related Person entity
     */

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

    public function getClass(): Classe
    {
        return $this->getPerson()->getClass();
    }

    public function getType(): PersonType
    {
        return $this->getPerson()->getType();
    }

    public function isDirector(): bool
    {
        return $this->getPerson()->isDirector();
    }

}
