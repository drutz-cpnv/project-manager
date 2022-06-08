<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FileRepository::class)]
/**
 * @Vich\Uploadable()
 */
class File
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $title;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $filename;

    /**
     * @var \Symfony\Component\HttpFoundation\File\File
     */
    #[Assert\File(
        mimeTypes: [
            "application/pdf",
            "application/msword",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            "application/vnd.ms-excel",
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "application/zip",
            "application/vnd.ms-powerpoint",
            "application/vnd.openxmlformats-officedocument.presentationml.presentation",
        ]
    )]
    /**
     * @Vich\UploadableField(mapping="files", fileNameProperty="filename")
     */
    private $file;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'files')]
    private $createdBy;

    #[ORM\ManyToOne(targetEntity: Mandate::class, cascade: ["remove"], inversedBy: 'files')]
    private $mandate;

    #[ORM\ManyToOne(targetEntity: Project::class, cascade: ["remove"], inversedBy: 'files')]
    private $project;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTimeImmutable());
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

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

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

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

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

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return null|\Symfony\Component\HttpFoundation\File\File
     */
    public function getFile(): ?\Symfony\Component\HttpFoundation\File\File
    {
        return $this->file;
    }

    /**
     * @param null|\Symfony\Component\HttpFoundation\File\File $file
     * @return self
     */
    public function setFile(?\Symfony\Component\HttpFoundation\File\File $file): self
    {
        $this->file = $file;
        if(is_null($this->title)) {
            $this->setTitle($file->getFilename());
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }
}
