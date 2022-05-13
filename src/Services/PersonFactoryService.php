<?php

namespace App\Services;

use App\Entity\Classe;
use App\Entity\Person;
use App\Entity\PersonType;
use App\Repository\ClasseRepository;
use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\Date;

class PersonFactoryService
{

    private const API_TRANSLATE_TYPE = [
        'Cpnv::CurrentStudent' => 'student',
        'Cpnv::Teacher' => 'teacher'
    ];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ClasseRepository $classRepository,
        private PersonRepository $personRepository,
    )
    {
    }

    public function create($data, ?PersonType $type = null): Person
    {
        if(is_null($type)){
            $type = $this->entityManager->getRepository(PersonType::class)->findOneBy(['slug' => self::API_TRANSLATE_TYPE[$data->type]]);
        }

        $class = null;

        if($type->getSlug() === 'student') {
            $class = $this->classRepository->findOneBy(['name' => $data->current_class->link->name]);
        }

        return (new Person())
            ->setType($type)
            ->setEmail(strtolower($data->email))
            ->setFirstname($data->firstname)
            ->setLastname($data->lastname)
            ->setExternalId($data->id)
            ->setClass($class)
            ;
    }

    /**
     * @param ArrayCollection $data
     */
    public function updateFormAPI(ArrayCollection $data): void
    {
        foreach ($data as $apiPerson) {
            $person = $this->personRepository->findOneBy(['email' => $apiPerson->email]);

            if(is_null($person)) {
                $this->entityManager->persist($this->create($apiPerson));
                continue;
            }

            $this->singleUpdate($person, $apiPerson);
        }

        $this->entityManager->flush();
    }

    private function singleUpdate(Person $person, object $data)
    {
        if($person->isStudent()) {
            $distClass = $data->current_class->link->name;
            if (is_null($person->getClass()) || ($distClass->name !== $person->getClass()->getName())) $this->setClass($person, $distClass);
        }

        if ($person->getEmail() !== strtolower($data->email)) $person->setEmail(strtolower($data->email));
        if (is_null($person->getExternalId())) $person->setExternalId($data->id);
        if ($person->getLastname() !== $data->lastname) $person->setLastname($data->lastname);
        if ($person->getFirstname() !== $data->firstname) $person->setFirstname($data->firstname);
        $person->setUpdatedAt(new \DateTimeImmutable());
    }

    public function update(Person $person)
    {
        $person->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->flush();
    }

    private function setClass(Person $person, string $class)
    {
        $person->setClass($this->classRepository->findOneBy(['name' => $class]));
    }

}