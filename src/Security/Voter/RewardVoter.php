<?php

namespace App\Security\Voter;

use App\Entity\Reward;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class RewardVoter extends AbstractVoter
{
    public const CREATE = "REWARD_CREATE";
    public const READ = "REWARD_READ";
    public const UPDATE = "REWARD_UPDATE";
    public const DELETE = "REWARD_DELETE";

    public function __construct(private readonly Security $security)
    {
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /**
         * @var Reward $subject
         * @var UserInterface $user
         */
        if (!($user = $token->getUser()) instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::UPDATE:
                // is_granted('ROLE_USER') and object.getManager() == user
                // Seul le manager de la récompense peut la modifier
                if ($this->security->isGranted("ROLE_USER", $user)
                    && $subject->getManager() === $user) {
                    return true;
                }
                break;
            case self::DELETE:
                // is_granted('ROLE_ADMIN') or (is_granted('ROLE_USER') and object.getManager() == user)
                // Seul le manager de la récompense ou un administrateur peut la supprimer
                if ($this->security->isGranted("ROLE_ADMIN", $user)
                    || ($this->security->isGranted("ROLE_USER", $user)
                    && $subject->getManager() === $user)) {
                    return true;
                }
                break;
            case self::CREATE:
            case self::READ:
                return true;
        }
        return false;
    }

    protected function getSubjectClass(): string
    {
        return Reward::class;
    }
}