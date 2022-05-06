<?php

namespace App\Services;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;

class ClientService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    public function create(Client $client): void
    {
        $this->entityManager->persist($client);
        $this->entityManager->flush();
    }

}