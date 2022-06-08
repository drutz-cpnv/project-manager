<?php

namespace App\Security\Voter;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';
    public const SIMPLE_EDIT = 'SIMPLE_EDIT';
    public const FINAL_EVAL = 'FINAL_EVAL';
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
        return in_array($attribute, [self::EDIT, self::VIEW, self::SIMPLE_EDIT, self::FINAL_EVAL, self::EVALUATE_MEMBER])
            && $subject instanceof Project;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        /** @var Project $project */
        $project = $subject;

        // ... (check conditions and return true to grant permission) ...
        return match ($attribute) {
            self::SIMPLE_EDIT => $this->canSimpleEdit($project, $user),
            self::EDIT => $this->canEdit($project, $user),
            self::VIEW => $this->canView($project, $user),
            self::FINAL_EVAL => $this->canEvaluateFinish($project, $user),
            default => false
        };
    }


    private function canView(Project $project, User $user): bool
    {
        if ($this->canSimpleEdit($project, $user)) return true;
        if ($project->userIsMember($user)) return true;
        return false;
    }

    private function canEdit(Project $project, User $user): bool
    {
        if ($this->security->isGranted('ROLE_DIRECTOR', $user)) return true;
        return false;
    }

    private function canSimpleEdit(Project $project, User $user): bool
    {
        if ($this->canEdit($project, $user)) return true;
        if ($project->getManager()->getUser() === $user) return true;
        return false;
    }

    private function canEvaluateFinish(Project $project, User $user): bool
    {
        if ($this->security->isGranted('ROLE_COACH', $user) && $project->getCoach()->getId() === $user->getPerson()->getId() && $project->getTeacherEvaluation()->isEmpty()) return true;
        return false;
    }
}
