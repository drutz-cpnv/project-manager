<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Mandate::class, orphanRemoval: true)]
    private $mandates;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $lastname;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $firstname;

    #[ORM\Column(type: 'string', length: 255)]
    private $email;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $company;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $phoneNumber;

    #[ORM\OneToOne(inversedBy: 'client', targetEntity: Person::class, cascade: ['persist', 'remove'])]
    private $person;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $contact;

    public function __construct()
    {
        $this->mandates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Mandate>
     */
    public function getMandates(): Collection
    {
        return $this->mandates;
    }

    public function addMandate(Mandate $mandate): self
    {
        if (!$this->mandates->contains($mandate)) {
            $this->mandates[] = $mandate;
            $mandate->setClient($this);
        }

        return $this;
    }

    public function removeMandate(Mandate $mandate): self
    {
        if ($this->mandates->removeElement($mandate)) {
            // set the owning side to null (unless already changed)
            if ($mandate->getClient() === $this) {
                $mandate->setClient(null);
            }
        }

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname ?? $this->getPerson()->getLastname();
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname ?? $this->getPerson()->getFirstname();
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

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

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getFirstname() . " " . $this->getLastname();
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

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

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): self
    {
        $this->contact = $contact;

        return $this;
    }
}
