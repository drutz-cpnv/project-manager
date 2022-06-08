<?php

namespace App\Services;

use App\Data\Settings;
use Doctrine\ORM\EntityManagerInterface;

class SettingsService
{

    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {}


    public function update(Settings $settings)
    {


    }

}