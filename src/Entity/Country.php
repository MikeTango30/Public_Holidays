<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CountryRepository")
 */
class Country
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $country;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Holidays", mappedBy="country", orphanRemoval=true)
     */
    private $yes;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $countryCode;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $regions = [];

    public function __construct()
    {
        $this->yes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collection|Holidays[]
     */
    public function getYes(): Collection
    {
        return $this->yes;
    }

    public function addYe(Holidays $ye): self
    {
        if (!$this->yes->contains($ye)) {
            $this->yes[] = $ye;
            $ye->setCountry($this);
        }

        return $this;
    }

    public function removeYe(Holidays $ye): self
    {
        if ($this->yes->contains($ye)) {
            $this->yes->removeElement($ye);
            // set the owning side to null (unless already changed)
            if ($ye->getCountry() === $this) {
                $ye->setCountry(null);
            }
        }

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getRegions(): ?array
    {
        return $this->regions;
    }

    public function setRegions(?array $regions): self
    {
        $this->regions = $regions;

        return $this;
    }

}
