<?php

namespace App\Entity;

use App\Repository\InstallationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstallationRepository::class)]
class Installation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $serveur = null;

    #[ORM\Column(length: 255)]
    private ?string $domaine = null;

    #[ORM\Column(length: 255)]
    private ?string $sous_domaine = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dossier = null;

    // #[ORM\OneToMany(mappedBy: 'installation', targetEntity: InstallationEntreprise::class)]
    // private Collection $installationEntreprises;

    #[ORM\OneToMany(mappedBy: 'installation', targetEntity: Entreprise::class)]
    private Collection $entreprises;

    public function __construct()
    {
        // $this->installationEntreprises = new ArrayCollection();
        $this->entreprises = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nom;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getnom(): ?string
    {
        return $this->nom;
    }

    public function setnom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getServeur(): ?string
    {
        return $this->serveur;
    }

    public function setServeur(string $serveur): self
    {
        $this->serveur = $serveur;

        return $this;
    }

    public function getDomaine(): ?string
    {
        return $this->domaine;
    }

    public function setDomaine(string $domaine): self
    {
        $this->domaine = $domaine;

        return $this;
    }

    public function getSousDomaine(): ?string
    {
        return $this->sous_domaine;
    }

    public function setSousDomaine(string $sous_domaine): self
    {
        $this->sous_domaine = $sous_domaine;

        return $this;
    }

    public function getDossier(): ?string
    {
        return $this->dossier;
    }

    public function setDossier(string $dossier): self
    {
        $this->dossier = $dossier;

        return $this;
    }

    /**
     * @return Collection<int, InstallationEntreprise>
     */
    // public function getInstallationEntreprises(): Collection
    // {
    //     return $this->installationEntreprises;
    // }

    // public function addInstallationEntreprise(InstallationEntreprise $installationEntreprise): self
    // {
    //     if (!$this->installationEntreprises->contains($installationEntreprise)) {
    //         $this->installationEntreprises->add($installationEntreprise);
    //         $installationEntreprise->setInstallation($this);
    //     }

    //     return $this;
    // }

    // public function removeInstallationEntreprise(InstallationEntreprise $installationEntreprise): self
    // {
    //     if ($this->installationEntreprises->removeElement($installationEntreprise)) {
    //         // set the owning side to null (unless already changed)
    //         if ($installationEntreprise->getInstallation() === $this) {
    //             $installationEntreprise->setInstallation(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection<int, Entreprise>
     */
    public function getEntreprises(): Collection
    {
        return $this->entreprises;
    }

    public function addEntreprise(Entreprise $entreprise): self
    {
        if (!$this->entreprises->contains($entreprise)) {
            $this->entreprises->add($entreprise);
            $entreprise->setInstallation($this);
        }

        return $this;
    }

    public function removeEntreprise(Entreprise $entreprise): self
    {
        if ($this->entreprises->removeElement($entreprise)) {
            // set the owning side to null (unless already changed)
            if ($entreprise->getInstallation() === $this) {
                $entreprise->setInstallation(null);
            }
        }

        return $this;
    }
}