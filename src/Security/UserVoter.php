<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    const OWNER = 'owner';

    protected function supports(string $attribute, $subject)
    {
        if (!in_array($attribute, [self::OWNER])) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $owner, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var User $owner */

        switch ($attribute) {
            case self::OWNER:
                return $this->isOwner($owner, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function isOwner(User $owner, User $user)
    {
        return $user === $owner;
    }
}
