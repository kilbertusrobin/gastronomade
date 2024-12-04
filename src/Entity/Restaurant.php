<?php

namespace App\Entity;

use App\Repository\RestaurantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestaurantRepository::class)]
class Restaurant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['list_restaurant'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['list_restaurant'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['list_restaurant'])]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    #[Groups(['list_restaurant'])]
    private ?string $postalCode = null;

    #[ORM\Column(length: 255)]
    #[Groups(['list_restaurant'])]
    private ?string $adress = null;

    #[ORM\Column(length: 255)]
    #[Groups(['list_restaurant'])]
    private ?string $phone = null;

    #[ORM\Column]
    #[Groups(['list_restaurant'])]
    private ?float $lat = null;

        #[ORM\Column]
        #[Groups(['list_restaurant'])]
    private ?float $longitude = null;

        /**
         * @var Collection<int, FlagshipDish>
         */
        #[ORM\OneToMany(targetEntity: FlagshipDish::class, mappedBy: 'restaurant')]
        private Collection $flagshipDishes;

        public function __construct()
        {
            $this->flagshipDishes = new ArrayCollection();
        }

    public function getId(): ?int
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

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): static
    {
        $this->adress = $adress;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): static
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return Collection<int, FlagshipDish>
     */
    public function getFlagshipDishes(): Collection
    {
        return $this->flagshipDishes;
    }

    public function addFlagshipDish(FlagshipDish $flagshipDish): static
    {
        if (!$this->flagshipDishes->contains($flagshipDish)) {
            $this->flagshipDishes->add($flagshipDish);
            $flagshipDish->setRestaurant($this);
        }

        return $this;
    }

    public function removeFlagshipDish(FlagshipDish $flagshipDish): static
    {
        if ($this->flagshipDishes->removeElement($flagshipDish)) {
            // set the owning side to null (unless already changed)
            if ($flagshipDish->getRestaurant() === $this) {
                $flagshipDish->setRestaurant(null);
            }
        }

        return $this;
    }
}
