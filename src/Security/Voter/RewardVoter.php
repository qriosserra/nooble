<?php /** @noinspection PhpUnused */

namespace App\Security\Voter;

use App\Entity\Reward;
use App\Exception\UnexpectedVoterAttributeException;
use App\Security\Roles;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class RewardVoter extends AbstractVoter
{
    public const CREATE = "REWARD_CREATE";
    public const READ = "REWARD_READ";
    public const UPDATE = "REWARD_UPDATE";
    public const DELETE = "REWARD_DELETE";

    public function __construct(private readonly Security $security)
    {
    }

    /**
     * @param string $attribute
     * @param Reward $subject
     * @param TokenInterface $token
     * @return bool
     * @throws UnexpectedVoterAttributeException
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return match ($attribute) {
            /**
             * Seul le créateur de la récompense peut la modifier
             */
            self::UPDATE =>
                ($user = $this->returnUserOrFalse($token))
                && ($this->security->isGranted(Roles::ORGANISER, $user)
                    && $subject->getCreator() === $user),

            /**
             * Seul le créateur de la récompense ou un administrateur peut la supprimer
             */
            self::DELETE =>
                ($user = $this->returnUserOrFalse($token))
                && ($this->security->isGranted(Roles::ADMIN, $user)
                    || ($this->security->isGranted(Roles::ORGANISER, $user)
                        && $subject->getCreator() === $user)),

            default => throw new UnexpectedVoterAttributeException($attribute),
        };
    }

    protected function getSubjectClass(): string
    {
        return Reward::class;
    }
}
