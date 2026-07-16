<?php

namespace App\Entity;

use App\Repository\WineCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "daa_wine_category")]
#[ORM\Entity(repositoryClass: WineCategoryRepository::class)]
class WineCategory
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

    #[ORM\Column(name: "slug", type: "string")]
    private ?string $slug = null;

    #[ORM\Column(name: "priority", type: "integer")]
    private int $priority = 0;

    // ONE TO MANY
    #[ORM\OneToMany(targetEntity: Wine::class, mappedBy: "category")]
    #[ORM\OrderBy(["priority" => "ASC", "name" => "ASC"])]
    private Collection $wines;
    //

    public function __construct()
    {
        $this->wines = new ArrayCollection();
    }

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

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
     * @return Collection<int, Wine>
     */
    public function getWines(): Collection
    {
        return $this->wines;
    }

    public function addWine(Wine $wine): static
    {
        if (!$this->wines->contains($wine)) {
            $this->wines->add($wine);
            $wine->setCategory($this);
        }

        return $this;
    }

    public function removeWine(Wine $wine): static
    {
        if ($this->wines->removeElement($wine)) {
            if ($wine->getCategory() === $this) {
                $wine->setCategory(null);
            }
        }

        return $this;
    }
}
