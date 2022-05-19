<?php

namespace App\Security\Voter;

use App\Entity\UserOwnedInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserOwnedVoter extends Voter
{
    public const EDIT = 'CAN_EDIT';
    public const VIEW = 'CAN_VIEW';

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html // , self::VIEW
        return in_array($attribute, [self::EDIT])
            && $subject instanceof UserOwnedInterface;
    }

    /**
     * @param UserOwnedInterface $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        $owner = $subject->getUser();
        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                return $owner && $owner->getId() === $user->getId();
                break;
            /*case self::VIEW:
                // logic to determine if the user can VIEW
                // return true or false
                break;*/
        }

        return false;
    }
}
