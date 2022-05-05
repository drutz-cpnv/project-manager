<?php

namespace App\Entity;

use App\Repository\MandateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MandateRepository::class)]
class Mandate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'text')]
    private $description;

    #[ORM\Column(type: 'date_immutable')]
    private $desiredDate;

    #[ORM\Column(type: 'integer')]
    private $uid;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'mandates')]
    #[ORM\JoinColumn(nullable: false)]
    private $client;

    #[ORM\Column(type: 'boolean')]
    private $isDelegated;

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

    public function setDesiredDate(\DateTimeImmutable $desiredDate): self
    {
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
}
