<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Constraints\Regex;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[UniqueEntity('login')]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[NotBlank]
    #[Length(min: 5, max: 50, minMessage: 'Le login doit contenir au moins 5 caractères')]
    #[Regex('/^[a-zA-Z0-9]+$/', message: 'Le login ne doit contenir que des caractères alphanumériques.')]
    private ?string $login = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[PasswordStrength(message: 'Le mot de passe saisi n\'est pas assez fort !')]
    private ?string $password = null;

    /**
     * @var Collection<int, Compte>
     */
    #[ORM\OneToMany(targetEntity: Compte::class, mappedBy: 'titulaire', orphanRemoval: true)]
    private Collection $comptes;

    /**
     * @var Collection<int, Famille>
     */
    #[ORM\OneToMany(targetEntity: Famille::class, mappedBy: 'utilisateur')]
    private Collection $familles;

    /**
     * @var Collection<int, Partage>
     */
    #[ORM\OneToMany(targetEntity: Partage::class, mappedBy: 'utilisateur', orphanRemoval: true)]
    private Collection $partages;

    /**
     * @var Collection<int, Operation>
     */
    #[ORM\OneToMany(targetEntity: Operation::class, mappedBy: 'utilisateur')]
    private Collection $operations;  // <-- Define the operations property

    #[ORM\Column(length: 255)]
    private ?string $roles = null;

    #[ORM\Column]
    private ?float $solde;

    public function __construct()
    {
        $this->operations = new ArrayCollection(); // Initialize the collection
        $this->comptes = new ArrayCollection();
        $this->familles = new ArrayCollection();
        $this->partages = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, Compte>
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(Compte $compte): static
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes->add($compte);
            $compte->setTitulaire($this);
        }

        return $this;
    }

    public function removeCompte(Compte $compte): static
    {
        if ($this->comptes->removeElement($compte)) {
            // set the owning side to null (unless already changed)
            if ($compte->getTitulaire() === $this) {
                $compte->setTitulaire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Famille>
     */
    public function getFamilles(): Collection
    {
        return $this->familles;
    }

    public function addFamille(Famille $famille): static
    {
        if (!$this->familles->contains($famille)) {
            $this->familles->add($famille);
            $famille->setUtilisateur($this);
        }

        return $this;
    }

    public function removeFamille(Famille $famille): static
    {
        if ($this->familles->removeElement($famille)) {
            // set the owning side to null (unless already changed)
            if ($famille->getUtilisateur() === $this) {
                $famille->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Partage>
     */
    public function getPartages(): Collection
    {
        return $this->partages;
    }

    public function addPartage(Partage $partage): static
    {
        if (!$this->partages->contains($partage)) {
            $this->partages->add($partage);
            $partage->setUtilisateur($this);
        }

        return $this;
    }

    public function removePartage(Partage $partage): static
    {
        if ($this->partages->removeElement($partage)) {
            // set the owning side to null (unless already changed)
            if ($partage->getUtilisateur() === $this) {
                $partage->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getRoles(): ?string
    {
        return $this->roles;
    }

    public function setRoles(string $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSolde(): ?float
    {
        return $this->solde;
    }

    public function setSolde(float $solde): self
    {
        $this->solde = $solde;

        return $this;
    }
    /**
     * @return Collection|Operation[]
     */
    public function getOperations(): Collection
    {
        return $this->operations;
    }

    public function addOperation(Operation $operation): self
    {
        if (!$this->operations->contains($operation)) {
            $this->operations[] = $operation;
            $operation->setUtilisateur($this); // Set the utilisateur for the operation
        }

        return $this;
    }

    public function removeOperation(Operation $operation): self
    {
        if ($this->operations->removeElement($operation)) {
            // Set the owning side to null (unless already changed)
            if ($operation->getUtilisateur() === $this) {
                $operation->setUtilisateur(null);
            }
        }

        return $this;
    }
}
