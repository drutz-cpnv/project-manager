<?php

namespace App\Data;

class Settings
{

    private $settings;

    /**
     * @return mixed
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param mixed $settings
     * @return Settings
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
        return $this;
    }




}