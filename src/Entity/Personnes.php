<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PersonnesRepository;
use App\State\PersonneStateProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PersonnesRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(provider: PersonneStateProvider::class),
        new Post()
    ],
    order: ['nom' => 'asc', 'prenom' => 'asc'],
    paginationEnabled: false,
)]
class Personnes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank]
    #[Assert\GreaterThan(
        value: "today - 100 years",
        message: "Attention seule les personnes de moins de 100 ans peuvent être enregistrées"
    )]
    private ?\DateTimeInterface $naissance = null;

    #[ORM\OneToMany(mappedBy: 'personne', targetEntity: Emplois::class)]
    private Collection $emplois;

    public function __construct()
    {
        $this->emplois = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNaissance(): ?\DateTimeInterface
    {
        return $this->naissance;
    }

    public function setNaissance(\DateTimeInterface $naissance): static
    {
        $this->naissance = $naissance;

        return $this;
    }

    /**
     * @return Collection<int, Emplois>
     */
    public function getEmplois(): Collection
    {
        return $this->emplois;
    }

    public function addEmploi(Emplois $emploi): static
    {
        if (!$this->emplois->contains($emploi)) {
            $this->emplois->add($emploi);
            $emploi->setPersonne($this);
        }

        return $this;
    }

    public function removeEmploi(Emplois $emploi): static
    {
        if ($this->emplois->removeElement($emploi)) {
            // set the owning side to null (unless already changed)
            if ($emploi->getPersonne() === $this) {
                $emploi->setPersonne(null);
            }
        }

        return $this;
    }
}
