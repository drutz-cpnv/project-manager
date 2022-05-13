<?php

namespace App\Services;

use App\Entity\Person;
use App\Services\Intranet\IntranetClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class UpdateService
{


    public function __construct(
        private EntityManagerInterface $entityManager,
        private IntranetClient $intranetClient,
        private PersonFactoryService $personFactory,
        private Security $security,
    )
    {
    }

    public function students()
    {
        $this->personFactory->updateFormAPI($this->intranetClient->findAllStudents());
    }

}