<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Enum\RewardType;
use App\Repository\RewardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RewardRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            // security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object == user)",
        ),
        new Post(
            // security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object == user)",
        ),
        new Patch(
            security: "(is_granted('ROLE_USER') and object.getManager() == user)",
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getManager() == user)",
        ),
    ]
)]
class Reward
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["event:read"])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(["event:read"])]
    private ?string $description = null;

    #[ORM\Column(enumType: RewardType::class)]
    #[Groups(["event:read"])]
    private ?RewardType $rewardType = null;

    /**
     * @var Collection<int, PrizePack>
     */
    #[ORM\OneToMany(targetEntity: PrizePack::class, mappedBy: 'reward', orphanRemoval: false)]
    private Collection $prizePacks;

    #[ORM\ManyToOne(inversedBy: 'rewards')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $manager = null;


    public function __construct()
    {
        $this->prizePacks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getRewardType(): ?RewardType
    {
        return $this->rewardType;
    }

    public function setRewardType(RewardType $rewardType): static
    {
        $this->rewardType = $rewardType;

        return $this;
    }

    /**
     * @return Collection<int, PrizePack>
     */
    public function getPrizePacks(): Collection
    {
        return $this->prizePacks;
    }

    public function addPrizePack(PrizePack $prizePack): static
    {
        if (!$this->prizePacks->contains($prizePack)) {
            $this->prizePacks->add($prizePack);
            $prizePack->setReward($this);
        }

        return $this;
    }

    public function removePrizePack(PrizePack $prizePack): static
    {
        if ($this->prizePacks->removeElement($prizePack)) {
            // set the owning side to null (unless already changed)
            if ($prizePack->getReward() === $this) {
                $prizePack->setReward(null);
            }
        }

        return $this;
    }

    public function getManager(): ?User
    {
        return $this->manager;
    }

    public function setManager(?User $manager): static
    {
        $this->manager = $manager;

        return $this;
    }
}
