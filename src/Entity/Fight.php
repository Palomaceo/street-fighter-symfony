<?php

namespace App\Entity;

use App\Repository\FightRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FightRepository::class)]
class Fight
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Character $myCharacter = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Character $opponentCharacter = null;

    #[ORM\Column]
    private ?bool $IsMyTurn = null;

    #[ORM\ManyToOne(inversedBy: 'fights')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMyCharacter(): ?Character
    {
        return $this->myCharacter;
    }

    public function setMyCharacter(?Character $myCharacter): static
    {
        $this->myCharacter = $myCharacter;

        return $this;
    }

    public function getOpponentCharacter(): ?Character
    {
        return $this->opponentCharacter;
    }

    public function setOpponentCharacter(?Character $opponentCharacter): static
    {
        $this->opponentCharacter = $opponentCharacter;

        return $this;
    }

    public function isMyTurn(): ?bool
    {
        return $this->IsMyTurn;
    }

    public function setIsMyTurn(bool $IsMyTurn): static
    {
        $this->IsMyTurn = $IsMyTurn;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
