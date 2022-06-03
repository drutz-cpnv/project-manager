<?php

namespace App\Services;

use App\Entity\Person;
use App\Entity\User;
use App\Repository\PersonRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

class UserService
{

    public function __construct(
        private UserRepository $userRepository,
        private PersonRepository $personRepository,
        private RoleRepository $roleRepository,
        private EntityManagerInterface $em,
        private Security $security,
        private UserPasswordHasherInterface $userPasswordHasher,
    )
    {
    }

    public function toggleDirector(User $user)
    {
        if($user->isDirector()) {
            $this->removeRole('ROLE_DIRECTOR', $user);
        } else {
            $this->addRole('ROLE_DIRECTOR', $user);
        }
        $this->persistUpdate($user);
    }

    public function testCreate(Person $person, $defaultRoles = ["ROLE_STUDENT"])
    {
        if($person->getUser() !== null) return;
        $user = (new User())
            ->setPerson($person)
            ->setEmail($person->getEmail())
            ->setIsVerified(true)
        ;

        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                "123456789"
        ));

        foreach ($defaultRoles as $role) {
            $this->addRole($role, $user);
        }

        $this->em->persist($user);
        $this->em->flush();
    }

    public function addRole(string $role, ?User $user)
    {
        if(is_null($user)) return;
        if(in_array($role, $user->getRoles())) return;
        $role = $this->roleRepository->findByName($role);
        $user->addRole($role);
        $user->setUpdatedAt(new \DateTimeImmutable());
    }

    public function removeRole(string $role, ?User $user)
    {
        if(is_null($user)) return;
        if(!in_array($role, $user->getRoles())) return;
        $role = $this->roleRepository->findByName($role);
        $user->removeRole($role);
        $user->setUpdatedAt(new \DateTimeImmutable());
    }

    public function ban(User $user) {
        $user->setBannedAt(new \DateTimeImmutable());
        $this->persistUpdate($user);
    }

    public function unban(User $user) {
        $user->setBannedAt(null);
        $this->persistUpdate($user);
    }


    public function persistUpdate(User $user)
    {
        $user->setUpdatedAt(new \DateTimeImmutable());
        $user->setUpdatedBy($this->security->getUser());
        $this->em->flush();
    }


}