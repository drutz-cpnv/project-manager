<?php

namespace App\Services;

use App\Entity\Person;
use App\Entity\PersonType;
use Doctrine\ORM\EntityManagerInterface;

class PersonFactoryService
{

    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function create($data, string|PersonType $type): Person
    {
        if(!$type instanceof PersonType){
            $type = $this->entityManager->getRepository(PersonType::class)->findOneBy(['slug' => $type]);
        }

        return (new Person())
            ->setType($type)
            ->setEmail($data->email)
            ->setFirstname($data->firstname)
            ->setLastname($data->lastname)
            ;
    }

}