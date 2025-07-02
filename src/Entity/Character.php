<?php

namespace App\Entity;

use App\Repository\CharacterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CharacterRepository::class)]
#[ORM\Table(name: '`character`')]
class Character
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column]
    private ?int $life = null;

    #[ORM\Column]
    private ?int $speed = null;

    #[ORM\Column]
    private ?int $strength = null;

    #[ORM\Column]
    private ?int $regeneration = null;

    #[ORM\Column]
    private ?int $resistance = null;

    #[ORM\Column]
    private ?int $endurance = null;

    #[ORM\Column]
    private ?int $critical = null;

    #[ORM\ManyToOne(inversedBy: 'characters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, Skill>
     */
    #[ORM\ManyToMany(targetEntity: Skill::class, inversedBy: 'characters')]
    private Collection $skills;

    /**
     * @var Collection<int, Fight>
     */
    #[ORM\OneToMany(targetEntity: Fight::class, mappedBy: 'myCharacter')]
    private Collection $fights;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
        $this->fights = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getLife(): ?int
    {
        return $this->life;
    }

    public function getSpeed(): ?int
    {
        return $this->speed;
    }

    public function getStrength(): ?int
    {
        return $this->strength;
    }

    public function getRegeneration(): ?int
    {
        return $this->regeneration;
    }

    public function getResistance(): ?int
    {
        return $this->resistance;
    }

    public function getEndurance(): ?int
    {
        return $this->endurance;
    }

    public function getCritical(): ?int
    {
        return $this->critical;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function setLife(int $life): static
    {
        $this->life = $life;

        return $this;
    }

    public function setSpeed(int $speed): static
    {
        $this->speed = $speed;

        return $this;
    }

    public function setStrength(int $strength): static
    {
        $this->strength = $strength;

        return $this;
    }

    public function setRegeneration(int $regeneration): static
    {
        $this->regeneration = $regeneration;

        return $this;
    }

    public function setResistance(int $resistance): static
    {
        $this->resistance = $resistance;

        return $this;
    }

    public function setEndurance(int $endurance): static
    {
        $this->endurance = $endurance;

        return $this;
    }

    public function setCritical(int $critical): static
    {
        $this->critical = $critical;

        return $this;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Skill>
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skill $skill): static
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
        }

        return $this;
    }

    public function removeSkill(Skill $skill): static
    {
        $this->skills->removeElement($skill);

        return $this;
    }
}
