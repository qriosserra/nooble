<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\Repository\ParticipationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
//TODO: RAJOUTER Game.
#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: "/game/{id}/participations",
            uriVariables: [
                "id" => new Link(
                    fromProperty: "participations",
                    fromClass: Game::class
                )
            ]
        ),
        new GetCollection(
            uriTemplate: "/event/{id}/participations",
            uriVariables: [
                "id" => new Link(
                    fromProperty: "participations",
                    fromClass: Event::class
                )
            ]
        )
    ],
    normalizationContext: ["groups" => ["participation:read"]]
)]
class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Encounter>
     */
    #[ORM\OneToMany(targetEntity: Encounter::class, mappedBy: 'participation', orphanRemoval: true)]
    #[Groups(["participations:read", "participation:read"])]
    private Collection $encounters;

    #[ORM\Column]
    #[Groups(["participations:read", "participation:read"])]
    private ?int $round = null;

    #[ORM\ManyToOne(inversedBy: 'participations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    #[ORM\ManyToOne(inversedBy: 'participations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["participations:read", "participation:read"])]
    private ?Game $game = null;

    public function __construct()
    {
        $this->encounters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * @return Collection<int, Encounter>
     */
    public function getEncounters(): Collection
    {
        return $this->encounters;
    }

    public function addEncounter(Encounter $encounter): static
    {
        if (!$this->encounters->contains($encounter)) {
            $this->encounters->add($encounter);
            $encounter->setParticipation($this);
        }

        return $this;
    }

    public function removeEncounter(Encounter $encounter): static
    {
        if ($this->encounters->removeElement($encounter)) {
            // set the owning side to null (unless already changed)
            if ($encounter->getParticipation() === $this) {
                $encounter->setParticipation(null);
            }
        }

        return $this;
    }

    public function getRound(): ?int
    {
        return $this->round;
    }

    public function setRound(int $round): static
    {
        $this->round = $round;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }
}
