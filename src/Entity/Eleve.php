<?php

namespace App\Entity;

use App\Repository\EleveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EleveRepository::class)]
class Eleve extends Utilisateur
{
    #[ORM\Column(length: 255)]
    private ?string $identificationNationale = null;

    #[ORM\ManyToOne(inversedBy: 'eleves')]
    private ?Classe $classe = null;

    #[ORM\ManyToOne(inversedBy: 'eleves', cascade: ['persist'])]
    private ?Parents $parents = null;

    /**
     * @var Collection<int, Paiement>
     */
    #[ORM\OneToMany(targetEntity: Paiement::class, mappedBy: 'eleve')]
    private Collection $paiements;

    public function __construct()
    {
        $this->paiements = new ArrayCollection();
    }

    public function getIdentificationNationale(): ?string
    {
        return $this->identificationNationale;
    }

    public function setIdentificationNationale(string $identificationNationale): static
    {
        $this->identificationNationale = $identificationNationale;

        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): static
    {
        $this->classe = $classe;

        return $this;
    }

    public function getParents(): ?Parents
    {
        return $this->parents;
    }

    public function setParents(?Parents $parents): static
    {
        $this->parents = $parents;

        return $this;
    }

    /**
     * @return Collection<int, Paiement>
     */
    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function addPaiement(Paiement $paiement): static
    {
        if (!$this->paiements->contains($paiement)) {
            $this->paiements->add($paiement);
            $paiement->setEleve($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): static
    {
        if ($this->paiements->removeElement($paiement)) {
            // set the owning side to null (unless already changed)
            if ($paiement->getEleve() === $this) {
                $paiement->setEleve(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getNom().' '.$this->getPrenom().''.$this->getClasse();
    }

    #[ORM\PreRemove]
    public function preRemoveAction(EntityManagerInterface $em): void
    {
        $parent = $this->getParents();

        if (count($parent->getEleves()) === 1) {
            $em->remove($parent);
        }
    }
}
