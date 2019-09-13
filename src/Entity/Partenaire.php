<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\PartenaireRepository")
 */
class Partenaire
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"lister-partenaire"})
     * @Groups({"liste-userparte", "lister-partenaire"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ ne doit pas etre vide") 
     * @Groups({"lister-partenaire"})
     * @Groups({"liste-userparte","comptes", "lister-partenaire"})
     */
    private $nomEntreprise;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ ne doit pas etre vide")
     * @Groups({"lister-partenaire"})
     * @Groups({"liste-userparte", "comptes", "lister-partenaire"}) 
     */
    private $RS;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Ce champ ne doit pas etre vide") 
     * @Groups({"lister-partenaire"})
     * @Groups({"liste-userparte", "comptes", "lister-partenaire"})
     */
    private $NINEA;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ ne doit pas etre vide")
     * @Groups({"lister-partenaire"})
     * @Groups({"liste-userparte", "comptes", "lister-partenaire"}) 
     */
    private $Adresse;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"lister-partenaire"})
     * @Groups({"liste-userparte", "comptes", "lister-partenaire"}) 
     */
    private $Statut;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="partenaire")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Compte", mappedBy="partenaire")
     *@Groups({"comptes"})
     *@Groups({"lister-partenaire"})
     */
    private $comptes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="partenaire")
     */
    private $utilisateur;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->comptes = new ArrayCollection();
        $this->utilisateur = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEntreprise(): ?string
    {
        return $this->nomEntreprise;
    }

    public function setNomEntreprise(string $nomEntreprise): self
    {
        $this->nomEntreprise = $nomEntreprise;

        return $this;
    }

    public function getRS(): ?string
    {
        return $this->RS;
    }

    public function setRS(string $RS): self
    {
        $this->RS = $RS;

        return $this;
    }

    public function getNINEA(): ?string
    {
        return $this->NINEA;
    }

    public function setNINEA(string $NINEA): self
    {
        $this->NINEA = $NINEA;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->Adresse;
    }

    public function setAdresse(string $Adresse): self
    {
        $this->Adresse = $Adresse;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->Statut;
    }

    public function setStatut(string $Statut): self
    {
        $this->Statut = $Statut;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setPartenaire($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getPartenaire() === $this) {
                $user->setPartenaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Compte[]
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(Compte $compte): self
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes[] = $compte;
            $compte->setPartenaire($this);
        }

        return $this;
    }

    public function removeCompte(Compte $compte): self
    {
        if ($this->comptes->contains($compte)) {
            $this->comptes->removeElement($compte);
            // set the owning side to null (unless already changed)
            if ($compte->getPartenaire() === $this) {
                $compte->setPartenaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUtilisateur(): Collection
    {
        return $this->utilisateur;
    }

    public function addUtilisateur(User $utilisateur): self
    {
        if (!$this->utilisateur->contains($utilisateur)) {
            $this->utilisateur[] = $utilisateur;
            $utilisateur->setPartenaire($this);
        }

        return $this;
    }

    public function removeUtilisateur(User $utilisateur): self
    {
        if ($this->utilisateur->contains($utilisateur)) {
            $this->utilisateur->removeElement($utilisateur);
            // set the owning side to null (unless already changed)
            if ($utilisateur->getPartenaire() === $this) {
                $utilisateur->setPartenaire(null);
            }
        }

        return $this;
    }
}
