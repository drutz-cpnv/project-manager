<?php

namespace App\Helper\HTML;

class TableDay
{

    private ?\DateTimeImmutable $date = null;
    private bool $isToday = false;

    public function __construct(\DateTimeImmutable $date)
    {
        $this->date = $date;
        $today = new \DateTimeImmutable();

        if($this->date->format("Ymd") === $today->modify("this wednesday")->format("Ymd")) {
            $this->isToday = true;
        }
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function getNumber()
    {
        return $this->getDate()->format("d");
    }

    /**
     * @return bool
     */
    public function isToday(): bool
    {
        return $this->isToday;
    }



}