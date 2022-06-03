<?php

namespace App\Services;

use App\Entity\Person;
use App\Repository\ClasseRepository;
use App\Repository\PersonRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Services\Intranet\IntranetClient;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class UpdateService
{


    public function __construct(
        private EntityManagerInterface $entityManager,
        private IntranetClient $intranetClient,
        private PersonFactoryService $personFactory,
        private Security $security,
        private ClasseRepository $classeRepository,
        private RoleRepository $roleRepository,
        private UserRepository $userRepository,
        private PersonRepository $personRepository,
        private DefaultService $defaultService,
    )
    {
    }

    public function students(): void
    {
        $this->personFactory->updateFormAPI($this->intranetClient->findAllStudents());
    }

    public function classes()
    {
        //$apiClasses = $this->intranetClient->findAllClasses();

        $classes = $this->classeRepository->findAll();
        $parents = $this->classeRepository->findParentClasses();

        foreach ($parents as &$parent) {
            $id = (new ArrayCollection(explode('C', $parent->getName())))->last();
            $potentials = $this->classeRepository->findByClassId($id);
            foreach ($potentials as $key => $potential) {
                if(str_contains($potential->getName(), "SM-MI")) $parent->setChildren($potential);
            }
        }

        $this->entityManager->flush();
    }

    public function settings()
    {
        $this->defaultService->persistSettings();
    }

    public function addStudentRoleToStudents()
    {
        $studentRole = $this->roleRepository->findOneBy(['slug' => 'ROLE_STUDENT']);
        foreach ($this->personRepository->findAllStudents() as $student) {
            if(!is_null($student->getUser())) {
                $student->getUser()->addRole($studentRole);
            }
        }

        $this->entityManager->flush();
    }

}