<?php

namespace App\Entity;

use App\Repository\PeriodSummaryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PeriodSummaryRepository::class)]
class PeriodSummary
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(targetEntity: Period::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $period;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private $totalIncome;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private $totalExpenses;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private $balance;

    #[ORM\Column(type: 'datetime_immutable')]
    private $calculationDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPeriod(): ?Period
    {
        return $this->period;
    }

    public function setPeriod(Period $period): self
    {
        $this->period = $period;

        return $this;
    }

    public function getTotalIncome(): ?string
    {
        return $this->totalIncome;
    }

    public function setTotalIncome(string $totalIncome): self
    {
        $this->totalIncome = $totalIncome;

        return $this;
    }

    public function getTotalExpenses(): ?string
    {
        return $this->totalExpenses;
    }

    public function setTotalExpenses(string $totalExpenses): self
    {
        $this->totalExpenses = $totalExpenses;

        return $this;
    }

    public function getBalance(): ?string
    {
        return $this->balance;
    }

    public function setBalance(string $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function getCalculationDate(): ?\DateTimeImmutable
    {
        return $this->calculationDate;
    }

    public function setCalculationDate(\DateTimeImmutable $calculationDate): self
    {
        $this->calculationDate = $calculationDate;

        return $this;
    }
} 