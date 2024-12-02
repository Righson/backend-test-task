<?php

namespace App\Entity;

use App\Repository\TaxNumberRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Налоговые номера (их паттерны) хранятся в базе в виде пары паттерн-налог
*/
#[ORM\Entity(repositoryClass: TaxNumberRepository::class)]
class TaxNumber
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $pattern = null;

    #[ORM\Column]
    private ?int $tax = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPattern(): ?string
    {
        return $this->pattern;
    }

    public function setPattern(string $pattern): static
    {
        $this->pattern = $pattern;

        return $this;
    }

    public function getTax(): ?int
    {
        return $this->tax;
    }

    public function setTax(int $tax): static
    {
        $this->tax = $tax;

        return $this;
    }
}
