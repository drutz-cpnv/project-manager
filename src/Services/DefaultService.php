<?php

namespace App\Services;

use App\Entity\Person;
use App\Entity\PersonType;
use App\Entity\Role;
use App\Services\Intranet\IntranetClient;
use Doctrine\ORM\EntityManagerInterface;

class DefaultService
{

    private $toPersit = [];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private IntranetClient $intranetClient,
        private PersonFactoryService $personFactory,
     )
    {
    }

    public function all() {
        $this->roles();
        $this->personTypes();
        $this->students();
        $this->teachers();

        if(empty($this->toPersit)) return;

        foreach ($this->toPersit as $toPersit) {
            $this->entityManager->persist($toPersit);
        }

        $this->entityManager->flush();
    }

    public function students()
    {
        $default = [];

        foreach ($this->intranetClient->findAllStudents() as $student) {
            $default[] = $this->personFactory->create($student, 'student');
        }

        $this->defineToPersist($default, Person::class);

    }

    public function teachers()
    {
        $default = [];

        foreach ($this->intranetClient->findAllTeachers() as $teacher) {
            $default[] = $this->personFactory->create($teacher, 'teacher');
        }

        $this->defineToPersist($default, Person::class);

    }

    public function roles()
    {

        $default = [
            (new Role())->setName("Webmaster")->setSlug("ROLE_WEBMASTER"),
            (new Role())->setName("Administrateur")->setSlug("ROLE_ADMIN"),
            (new Role())->setName("Membre du COPIL")->setSlug("ROLE_COPIL"),
            (new Role())->setName("Directeur")->setSlug("ROLE_DIRECTOR"),
            (new Role())->setName("Enseignant")->setSlug("ROLE_TEACHER"),
            (new Role())->setName("Étudiant")->setSlug("ROLE_STUDENT"),
            (new Role())->setName("Client")->setSlug("ROLE_CLIENT"),
        ];

        $this->defineToPersist($default, Role::class);

    }

    public function personTypes()
    {

        $default = [
            (new PersonType())->setSlug('client')->setName("Client"),
            (new PersonType())->setSlug('student')->setName("Étudiant"),
            (new PersonType())->setSlug('teacher')->setName("Enseignant"),
        ];

        $this->defineToPersist($default, PersonType::class);

    }

    public function defineToPersist($default, string $class)
    {
        $repository = $this->entityManager->getRepository($class);
        $inDb = $repository->findAll();

        foreach ($default as $key => $def) {
            $toPersist = true;
            foreach ($inDb as $item) {
                if(method_exists($class, 'getSlug')) {
                    if($def->getSlug() === $item->getSlug()) {
                        $toPersist = false;
                        continue;
                    }
                } elseif (method_exists($class, 'getTitle')) {
                    if ($def->getTitle() === $item->getTitle()) {
                        $toPersist = false;
                        continue;
                    }
                } elseif (method_exists($class, 'getName')) {
                    if ($def->getName() === $item->getName()) {
                        $toPersist = false;
                        continue;
                    }
                } elseif (method_exists($class, 'getEmail')) {
                    if ($def->getEmail() === $item->getEmail()) {
                        $toPersist = false;
                        continue;
                    }
                }

            }
            if($toPersist) {
                $this->toPersit[] = $def;
            }
        }
    }

}