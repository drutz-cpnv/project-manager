<?php

namespace App\Services;

use App\Entity\Person;
use App\Services\Intranet\IntranetClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CopilService
{

    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
        private UserService $userService,
    )
    {
    }

    public function toggleCoaching(Person $person)
    {
        if($person->getType()->getSlug() !== "teacher") return;
        $person->setCanCoach(!$person->getCanCoach());
        if($person->getCanCoach()) {
            $this->userService->addRole('ROLE_COACH', $person->getUser());
        } else {
            $this->userService->removeRole('ROLE_COACH', $person->getUser());
        }
        $person->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();
    }

}