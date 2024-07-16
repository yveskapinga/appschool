<?php

namespace App\Entity;

use App\Repository\ClasseRepository;
use App\Trait\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClasseRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Classe
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $designation = null;

    /**
     * @var Collection<int, Eleve>
     */
    #[ORM\OneToMany(targetEntity: Eleve::class, mappedBy: 'classe')]
    private Collection $eleves;

    /**
     * @var Collection<int, Enseignant>
     */
    #[ORM\ManyToMany(targetEntity: Enseignant::class, mappedBy: 'classes')]
    private Collection $enseignants;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Enseignant $professeurPrincipal = null;

    #[ORM\Column]
    private ?int $classe = null;

    #[ORM\Column(length: 50)]
    private ?string $section = null;

    public function __construct()
    {
        $this->eleves = new ArrayCollection();
        $this->enseignants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(?string $designation): static
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * @return Collection<int, Eleve>
     */
    public function getEleves(): Collection
    {
        return $this->eleves;
    }

    public function addElefe(Eleve $elefe): static
    {
        if (!$this->eleves->contains($elefe)) {
            $this->eleves->add($elefe);
            $elefe->setClasse($this);
        }

        return $this;
    }

    public function removeElefe(Eleve $elefe): static
    {
        if ($this->eleves->removeElement($elefe)) {
            // set the owning side to null (unless already changed)
            if ($elefe->getClasse() === $this) {
                $elefe->setClasse(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Enseignant>
     */
    public function getEnseignants(): Collection
    {
        return $this->enseignants;
    }

    public function addEnseignant(Enseignant $enseignant): static
    {
        if (!$this->enseignants->contains($enseignant)) {
            $this->enseignants->add($enseignant);
            $enseignant->addClass($this);
        }

        return $this;
    }

    public function removeEnseignant(Enseignant $enseignant): static
    {
        if ($this->enseignants->removeElement($enseignant)) {
            $enseignant->removeClass($this);
        }

        return $this;
    }

    public function getProfesseurPrincipal(): ?Enseignant
    {
        return $this->professeurPrincipal;
    }

    public function setProfesseurPrincipal(?Enseignant $professeurPrincipal): static
    {
        $this->professeurPrincipal = $professeurPrincipal;

        return $this;
    }

    public function __toString()
    {
        return $this->getclasse().'e '.$this->getSection().' '.$this->getDesignation(); // $this->getProfesseurPrincipal()->getNom();
    }

    public function getclasse(): ?int
    {
        return $this->classe;
    }

    public function setclasse(int $classe): static
    {
        $this->classe = $classe;

        return $this;
    }

    public function getSection(): ?string
    {
        return $this->section;
    }

    public function setSection(string $section): static
    {
        $this->section = $section;

        return $this;
    }
}
