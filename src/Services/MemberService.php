<?php

namespace App\Services;

use App\Entity\Member;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class MemberService
{

    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
    )
    {
    }

    public function create(Member $member)
    {
        if ($member->getProject()->userIsMember($member->getUser())) return;
        $member->setCreatedAt(new \DateTimeImmutable());
        $member->setUpdatedAt(new \DateTimeImmutable());

        $this->em->persist($member);
        $this->em->flush();
    }

    public function update(Member $member)
    {
        $member->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();
    }

    public function delete(Member $member)
    {
        $this->em->remove($member);
        $this->em->flush();
    }

}