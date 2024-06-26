<?php

namespace App\Entity;

use App\Repository\PersonnelRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonnelRepository::class)]
class Personnel extends Utilisateur
{
    #[ORM\Column(length: 50)]
    private ?string $fonction = null;

    #[ORM\Column(length: 50)]
    private ?string $matricule = null;

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(string $fonction): static
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function __toString()
    {
        $this->getPrenom().' '.$this->getNom().' '.$this->fonction;
    }

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(string $matricule): static
    {
        $this->matricule = $matricule;

        return $this;
    }
}
