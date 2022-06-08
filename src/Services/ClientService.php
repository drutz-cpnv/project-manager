<?php

namespace App\Services;

use App\Entity\Client;
use App\Entity\Person;
use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\PersonRepository;
use App\Repository\PersonTypeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PersonTypeRepository $personTypeRepository,
        private readonly PersonRepository $personRepository,
        private readonly UserService $userService,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly ClientRepository $clientRepository,
        private readonly MailerService $mailerService,
    )
    {
    }


    public function create(User $user, FormInterface $form): void
    {

        $client = $this->clientRepository->findOneBy(['email' => $user->getEmail()]);
        $person = $this->personRepository->findOneBy(['email' => $user->getEmail()]);

        if(!is_null($client)) {
            $user->getPerson()->setClient($client);
        } else {
            $user->getPerson()->getClient()
                ->setFirstname($user->getPerson()->getFirstname())
                ->setLastname($user->getPerson()->getLastname())
                ->setEmail($user->getEmail())
            ;
        }

        if(!is_null($person)) {
            $user->setPerson($person);
        }
        else {
            $clientType = $this->personTypeRepository->findOneBy(['slug' => "client"]);
            $user->getPerson()
                ->setEmail($user->getEmail())
                ->setType($clientType)
            ;
        }

        $this->userService->addRole('ROLE_CLIENT', $user);

        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );

        //$this->mailerService->copilNewClient($client);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function createFromUser(User $user): Client
    {
        $this->userService->addRole("ROLE_CLIENT", $user);
        return (new Client())
            ->setPerson($user->getPerson())
            ->setEmail($user->getEmail())
            ->setLastname($user->getPerson()->getLastname())
            ->setFirstname($user->getPerson()->getFirstname())
            ;
    }

    public function createFromPerson(Person $person): Client
    {
        if(!is_null($person->getClient())) return $person->getClient();
        if(!is_null($person->getUser())) {
            $this->userService->addRole("ROLE_CLIENT", $person->getUser());
        }
        return (new Client())
            ->setPerson($person)
            ->setEmail($person->getEmail())
            ->setLastname($person->getLastname())
            ->setFirstname($person->getFirstname())
            ;
    }

    public function update(Client $client)
    {
        $this->entityManager->flush();
    }

}