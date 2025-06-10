<?php

namespace App\Entity;

use App\Repository\BudgetRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BudgetRepository::class)]
#[ORM\Table(name: 'budgets')]
class Budget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank(message: 'Le nom du budget ne peut pas être vide')]
    private ?string $name = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Le montant ne peut pas être vide')]
    #[Assert\Positive(message: 'Le montant doit être positif')]
    private ?string $amount = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\Range(min: 1, max: 12, notInRangeMessage: 'Le mois doit être entre {{ min }} et {{ max }}')]
    private ?int $month = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\Range(min: 2020, max: 2050, notInRangeMessage: 'L\'année doit être entre {{ min }} et {{ max }}')]
    private ?int $year = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'budgets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Category $category = null;

    // Getters et setters...
    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getAmount(): ?string { return $this->amount; }
    public function setAmount(string $amount): self { $this->amount = $amount; return $this; }

    public function getMonth(): ?int { return $this->month; }
    public function setMonth(int $month): self { $this->month = $month; return $this; }

    public function getYear(): ?int { return $this->year; }
    public function setYear(int $year): self { $this->year = $year; return $this; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }

    public function getCategory(): ?Category { return $this->category; }
    public function setCategory(?Category $category): self { $this->category = $category; return $this; }
}