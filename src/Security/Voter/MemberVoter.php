<?php

namespace App\Security\Voter;

use App\Entity\Member;
use App\Entity\Project;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class MemberVoter extends Voter
{

    public const EVALUATE_MEMBER = 'EVALUATE_MEMBER';

    public function __construct(
        private Security $security
    )
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EVALUATE_MEMBER])
            && $subject instanceof Member;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        /** @var Member $member */
        $member = $subject;

        // ... (check conditions and return true to grant permission) ...
        return match ($attribute) {
            self::EVALUATE_MEMBER => $this->canEvaluateMember($member, $user),
            default => false
        };
    }




    private function canEvaluateMember(Member $member, User $user): bool
    {
        if($this->security->isGranted("ROLE_COACH")) return true;
        return false;
    }
}
