<?php

namespace App\DataFixtures;

use App\Entity\Person;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $personRepository = $manager->getRepository(Person::class);

        $persons = $personRepository->findAll();

        foreach ($persons as $person) {
            if($person->getType()->getSlug() !== 'student' || !is_null($person->getUser())) continue;
            $user = (new User())
                ->setPerson($person)
                ->setEmail($person->getEmail())
                ->setIsVerified(true)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable())
            ;

            $user->setPassword($this->userPasswordHasher->hashPassword(
                $user,
                "123456789"
            ));

            $manager->persist($user);
        }

        $manager->flush();
    }
}
