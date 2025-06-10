<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\Email(message: 'Veuillez entrer un email valide')]
    #[Assert\NotBlank(message: 'L\'email ne peut pas être vide')]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private ?string $password = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank(message: 'Le nom ne peut pas être vide')]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Transaction::class, orphanRemoval: true)]
    private Collection $transactions;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Category::class, orphanRemoval: true)]
    private Collection $categories;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Budget::class, orphanRemoval: true)]
    private Collection $budgets;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->budgets = new ArrayCollection();
    }

    // Getters et setters...
    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getUserIdentifier(): string { return (string) $this->email; }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self { $this->roles = $roles; return $this; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }

    public function eraseCredentials(): void {}

    public function getTransactions(): Collection { return $this->transactions; }
    public function getCategories(): Collection { return $this->categories; }
    public function getBudgets(): Collection { return $this->budgets; }

    public function getTotalBalance(): float
    {
        $balance = 0;
        foreach ($this->transactions as $transaction) {
            if ($transaction->getType() === 'income') {
                $balance += floatval($transaction->getAmount());
            } else {
                $balance -= floatval($transaction->getAmount());
            }
        }
        return $balance;
    }
}