<?php

namespace App\Services;

use App\Entity\Note;
use App\Entity\PersonalEvaluation;
use App\Repository\MandateRepository;
use Doctrine\ORM\EntityManagerInterface;

class EvaluationService
{

    public function __construct(
        private EntityManagerInterface $em,
    )
    {
    }

    public function setStudentsNotesTemplates (PersonalEvaluation $personalEvaluation)
    {
        $personalEvaluation
            ->addNote(
                (new Note())
                    ->setName("Communication")
                    ->setCreatedAt(new \DateTimeImmutable())
            )
            ->addNote(
                (new Note())
                    ->setName("Attitude")
                    ->setCreatedAt(new \DateTimeImmutable()))
            ->addNote(
                (new Note())
                    ->setName(  "Respect des delais")
                    ->setCreatedAt(new \DateTimeImmutable()))
            ->addNote(
                (new Note())
                    ->setName("Proactivité")
                    ->setCreatedAt(new \DateTimeImmutable()));
    }


    public function create(PersonalEvaluation $evaluation)
    {
        $evaluation
            ->setCreatedAt(new \DateTimeImmutable())
        ;

        $this->em->persist($evaluation);
        $this->em->flush();
    }

}