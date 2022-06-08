<?php

namespace App\Services;

use App\Entity\Classe;
use App\Entity\Client;
use App\Entity\File;
use App\Entity\Mandate;
use App\Entity\Milestone;
use App\Entity\Project;
use App\Entity\TeamsRequest;
use App\Repository\ClasseRepository;
use App\Repository\FileRepository;
use App\Repository\PersonRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class SetupService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
        private PersonRepository $personRepository,
        private UserRepository $userRepository,
        private ClasseRepository $classeRepository,
        private FileRepository $fileRepository,
        private DefaultService $defaultService,
    )
    {}


    public function setup()
    {
        $this->defaultService->firstSetup();
        $this->defaultService->setup();
        $this->defaultService->persistSettings();
    }


    public function reset()
    {

        foreach ($this->userRepository->findAll() as $user) {
            $user->setUpdatedBy(null);
        }

        foreach ($this->classeRepository->findAll() as $class) {
            $class->setChildren(null);
        }

        foreach ($this->personRepository->findAll() as $person) {
            $person->setClass(null);
        }

        foreach ($this->fileRepository->findAll() as $file) {
            $file
                ->setCreatedBy(null)
                ->setProject(null)
                ->setMandate(null)
            ;
        }

        $this->entityManager->flush();

        $this->resetTable(Project::class);
        $this->resetTable(Mandate::class);
        $this->resetTable(TeamsRequest::class);
        $this->resetTable(Milestone::class);
        $this->resetTable(Client::class);
        $this->resetPersons();
        $this->resetTable(Classe::class);
        $this->entityManager->flush();

        $this->defaultService->all();
        
    }

    private function resetPersons()
    {
        foreach ($this->personRepository->findAll() as $person) {
            if(!is_null($person->getUser()) && $this->security->isGranted('ROLE_ADMIN', $person->getUser())) continue;
            if(!is_null($person->getUser())) {
                $this->entityManager->remove($person->getUser());
            }
            $this->entityManager->remove($person);
        }
    }


    private function resetTable(string $class)
    {
        $metadata = $this->entityManager->getClassMetadata($class);
        $repository = $this->entityManager->getRepository($class);

        foreach ($repository->findAll() as $item) {
            $this->entityManager->remove($item);
        }

        $this->entityManager->flush();

        $tableName = $metadata->getTableName();
        $connection = $this->entityManager->getConnection();
        $connection->exec("ALTER TABLE `". $tableName ."` AUTO_INCREMENT = 1");


    }
    
}