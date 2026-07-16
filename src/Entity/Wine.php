<?php

namespace App\Entity;

use App\Repository\WineRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "daa_wine")]
#[ORM\Entity(repositoryClass: WineRepository::class)]
class Wine
{
    public function __toString(): string
    {
        return (string) $this->name;
    }

    #[ORM\Column(name: "id", type: "bigint")]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?string $id = null;

    #[ORM\Column(name: "name", type: "string")]
    private ?string $name = null;

    #[ORM\Column(name: "priority", type: "integer")]
    private int $priority = 0;

    #[ORM\Column(name: "year", type: "integer", nullable: true)]
    private ?int $year = null;

    // MANY TO ONE
    #[ORM\ManyToOne(targetEntity: WineCategory::class, inversedBy: "wines")]
    #[ORM\JoinColumn(name: "category_id", referencedColumnName: "id")]
    private ?WineCategory $category = null;
    //

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Nome senza l'annata: l'anno viene mostrato a parte (chip), quindi lo
     * rimuoviamo dal nome per non ripeterlo.
     */
    public function getDisplayName(): ?string
    {
        if ($this->year === null) {
            return $this->name;
        }

        $name = preg_replace('/\b' . $this->year . '\b/', '', (string) $this->name);
        $name = preg_replace('/\s{2,}/', ' ', (string) $name);

        return trim((string) $name);
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getCategory(): ?WineCategory
    {
        return $this->category;
    }

    public function setCategory(?WineCategory $category): static
    {
        $this->category = $category;

        return $this;
    }
}
