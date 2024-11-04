<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: App\Repository\MaterielRepository::class)]
class Materiel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $reference;

    #[ORM\Column(type: 'string', length: 255)]
    private $type;

    #[ORM\Column(type: 'string', length: 255)]
    private $statut = 'Disponible';

    #[ORM\Column(type: 'string', length: 255)]
    private $processeur;

    #[ORM\Column(type: 'string', length: 255)]
    private $graphique;

    #[ORM\Column(type: 'string', length: 255)]
    private $mémoire;

    #[ORM\Column(type: 'string', length: 255)]
    private $série;

    #[ORM\Column(type: 'string', length: 255)]
    private $OS;

    #[ORM\Column(type: 'string', length: 255)]
    private $stockage;

    #[ORM\Column(type: 'string', length: 255)]
    private $taille
;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }
    public function getProcesseur(): ?string
    {
        return $this->processeur;
    }

    public function setProcesseur(string $processeur): self
    {
        $this->processeur = $processeur;
        return $this;
    }

    public function getGraphique(): ?string
    {
        return $this->graphique;
    }
    
    public function setGraphique(string $graphique): self
    {
        $this->graphique = $graphique;
        return $this;
    }

    public function getMémoire(): ?string
    {
        return $this->mémoire;
    }

    public function setMémoire(string $mémoire): self
    {
        $this->mémoire = $mémoire;
        return $this;
    }

    public function getSérie(): ?string
    {
        return $this->série;
    }
    
    public function setSérie(string $série): self
    {
        $this->série = $série;
        return $this;
    }

    public function getOS(): ?string
    {
        return $this->OS;
    }

    public function setOS(string $OS): self
    {
        $this->OS = $OS;
        return $this;
    }
    
    public function getStockage(): ?string
    {
        return $this->stockage;
    }

    public function setStockage(string $stockage): self
    {
        $this->stockage = $stockage;
        return $this;
    }

    public function getTaille(): ?string
    {
        return $this->taille;
    }

    public function setTaille(string $taille):self
    {
        $this->taille = $taille;
        return $this;
    }
}
