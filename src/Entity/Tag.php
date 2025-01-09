<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    /**
     * @var Collection<int, TagResto>
     */
    #[ORM\OneToMany(targetEntity: TagResto::class, mappedBy: 'tag')]
    private Collection $tagRestos;

    public function __construct()
    {
        $this->tagRestos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection<int, TagResto>
     */
    public function getTagRestos(): Collection
    {
        return $this->tagRestos;
    }

    public function addTagResto(TagResto $tagResto): static
    {
        if (!$this->tagRestos->contains($tagResto)) {
            $this->tagRestos->add($tagResto);
            $tagResto->setTag($this);
        }

        return $this;
    }

    public function removeTagResto(TagResto $tagResto): static
    {
        if ($this->tagRestos->removeElement($tagResto)) {
            // set the owning side to null (unless already changed)
            if ($tagResto->getTag() === $this) {
                $tagResto->setTag(null);
            }
        }

        return $this;
    }
}
