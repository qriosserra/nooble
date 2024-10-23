<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\SaucisseType;
use App\Repository\RegisterRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegisterRepository::class)]
#[ApiResource]
class Register
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'registers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'registers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    #[ORM\Column(enumType: SaucisseType::class)]
    private ?SaucisseType $saucisse = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    /**
     * Get the value of saucisse
     */
    public function getSaucisse()
    {
        return $this->saucisse;
    }

    /**
     * Set the value of saucisse
     *
     * @return  self
     */
    public function setSaucisse($saucisse)
    {
        $this->saucisse = $saucisse;

        return $this;
    }
}
