<?php

namespace App\Services\Intranet;

use Doctrine\Common\Collections\ArrayCollection;

interface IntranetClientInterface
{

    public function findAllStudents(): ArrayCollection;
    public function findOneStudent(string $query);

    public function findAllTeachers(): ArrayCollection;
    public function findOneTeacher(string $query);

    public function findAllClasses(): ArrayCollection;
    public function findOneClass(string $query);



}