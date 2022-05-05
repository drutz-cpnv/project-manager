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

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\ManyToMany(targetEntity: Person::class, inversedBy: 'clients')]
    private $representative;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Mandate::class, orphanRemoval: true)]
    private $mandates;

    public function __construct()
    {
        $this->representative = new ArrayCollection();
        $this->mandates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, Person>
     */
    public function getRepresentative(): Collection
    {
        return $this->representative;
    }

    public function addRepresentative(Person $representative): self
    {
        if (!$this->representative->contains($representative)) {
            $this->representative[] = $representative;
        }

        return $this;
    }

    public function removeRepresentative(Person $representative): self
    {
        $this->representative->removeElement($representative);

        return $this;
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
}
