<?php

namespace App\Security\Voter;

use App\Entity\Milestone;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectMilestoneVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';
    public const CREATE = 'CREATE';

    public function __construct(
        private Security $security
    )
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return
            in_array($attribute, [self::EDIT, self::VIEW, self::CREATE])
            && $subject instanceof Milestone;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }
        
        /** @var Milestone $milestone */
        $milestone = $subject;

        return match ($attribute) {
            self::EDIT => $this->canEdit($milestone, $user),
            self::VIEW => $this->canView($milestone, $user),
            default => false,
        };

    }

    private function canEdit(Milestone $milestone, User $user): bool
    {
        if ($this->security->isGranted('ROLE_DIRECTOR')) return true;
        $project = $milestone->getProject();
        return $project->getManager()->getUser() === $user;
    }

    private function canView(Milestone $milestone, User $user): bool
    {
        if ($this->canEdit($milestone, $user)) return true;
        if ($milestone->getProject()->userIsMember($user)) return true;
        return false;
    }
}
