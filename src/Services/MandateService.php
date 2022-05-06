<?php

namespace App\Services;

use App\Entity\Client;
use App\Entity\Mandate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;

class MandateService
{

    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function create(Mandate $mandate): void
    {
        $mandate
            ->setCreatedAt(new \DateTimeImmutable())
            ->setState(Mandate::STATE_PENDING_COPIL_CONFIRM)
        ;

        $this->entityManager->persist($mandate);
        $this->entityManager->flush();
    }

    public function acceptCOPIL(Mandate $mandate): void
    {
        $mandate->setState(Mandate::STATE_PENDING_DIR);
        $this->entityManager->flush();
    }

    public function acceptDIR(Mandate $mandate): void
    {
        $mandate->setState(Mandate::STATE_ACTIVE);
        $this->entityManager->flush();
    }

    public function refuse(Mandate $mandate): void
    {
        $mandate->setState(Mandate::STATE_REFUSED);
        $this->entityManager->flush();
    }

    public function terminate(Mandate $mandate): void
    {
        $mandate->setState(Mandate::STATE_TERMINATED);
        $this->entityManager->flush();
    }

    public function manualCreate(Mandate $mandate, FormInterface $form): void
    {
        $client = (new Client())
            ->setLastname($form->get('client_lastname')->getViewData())
            ->setFirstname($form->get('client_firstname')->getViewData())
            ->setEmail($form->get('client_email')->getViewData())
            ->setCompany($form->get('client_company')->getViewData())
            ->setPhoneNumber($form->get('client_phonenumber')->getViewData())
        ;
        $mandate->setClient($client);
        $this->create($mandate);
    }

}