<?php

namespace App\Helper\HTML;

use App\Data\Moment;
use App\Entity\Classe;
use Doctrine\Common\Collections\ArrayCollection;

class PlanningTable
{

    private \DateTimeImmutable $start;
    private \DateTimeImmutable $end;
    private Classe $class;
    private ?string $displayDay = "wednesday";
    private ?Moment $moment = null;

    private int $dayCount = 0;

    private int $todayIndex;
    private ArrayCollection $days;
    private ArrayCollection $months;
    private array $students;

    public function __construct()
    {
        $this->days = new ArrayCollection();
        $this->months = new ArrayCollection();
    }


    public function setDays()
    {
        $today = new \DateTime();
        $start = $this->start->modify("this ".$this->displayDay);
        $end = $this->end->modify("this ".$this->displayDay);

        $oneWeekInterval = new \DateInterval("P1W");

        $last = $start;

        $this->days[] = new TableDay($start);
        $this->addMonth($start);

        for ($i = 1; $i <= $end->diff($start)->days; $i++) {
            $date = clone $last->add($oneWeekInterval);
            $last = $date;
            $this->addMonth($date);
        }

        foreach ($this->months as $month) {
            $this->dayCount += $month['dayCount'];
        }

        $last = $start;

        for ($l = 1; $l <= $this->dayCount; $l++) {
            $date = clone $last->add($oneWeekInterval);
            $this->days->add(new TableDay($date));
            $last = $date;
        }

        return $this;
    }

    private function addMonth(\DateTimeImmutable $date)
    {
        $monthIndex = (int)$date->format("n");
        if($this->months->containsKey($monthIndex)) return;
        $this->months[$monthIndex] = [
            'label' => $date->format("M"),
            'dayCount' => $this->findMonthDayCount($date),
        ];
    }

    private function findMonthDayCount(\DateTimeImmutable $date): int
    {
        $monthIndex = (int)$date->format("n");
        $count = 0;
        $firstDayOfMonth = $date->modify("first ". $this->displayDay ." of this month");

        $countedDays = [];
        $days = [$firstDayOfMonth];
        for ($i = 1; $i < 5; $i++) {
            $interval = new \DateInterval("P".$i."W");
            $day = $firstDayOfMonth->add($interval);
            $days[] = $day;
        }

        foreach ($days as $day) {
            if($day > $this->start && $day < $this->end){
                if((int)$day->format("n") === $monthIndex) {
                    $count++;
                    $countedDays[] = $day;
                }
            }
        }

        return $count;
    }

    public function setStudents(): PlanningTable
    {
        foreach ($this->class->getStudents() as $student) {
            $s = (new Student())
                ->setStudent($student)
            ;

            $projects = [];

            foreach ($student->getProjects() as $project) {
                if(is_null($project->getSpecificationsEndDate())) continue;
                $p = (new Project($project));
                $p->setLength($this->moment, $this);
                $p->setOffset($this->moment, $this);
                $projects[] = $p;
            }

            $s->setProjects($projects);

            $this->students[] = $s;
        }

        return $this;
    }





















    /**
     * @param \DateTimeImmutable $start
     * @return PlanningTable
     */
    public function setStart(\DateTimeImmutable $start): PlanningTable
    {
        $this->start = $start;
        return $this;
    }

    /**
     * @param \DateTimeImmutable $end
     * @return PlanningTable
     */
    public function setEnd(\DateTimeImmutable $end): PlanningTable
    {
        $this->end = $end;
        return $this;
    }

    /**
     * @param Classe $class
     * @return PlanningTable
     */
    public function setClass(Classe $class): PlanningTable
    {
        $this->class = $class;
        return $this;
    }

    public function getClass(): Classe
    {
        return $this->class;
    }

    /**
     * @return TableDay[]
     */
    public function getDays(): array
    {
        return $this->days->toArray();
    }

    public function getMonths()
    {
        return $this->months;
    }

    public function getDisplayDay(): string
    {
        return $this->displayDay;
    }

    public function setMoment(Moment $moment): PlanningTable
    {
        $this->moment = $moment;
        $this->setStart($this->moment->getStartAt());
        $this->setEnd($this->moment->getEndAt());
        return $this;
    }

    /**
     * @return Student[]
     */
    public function getStudents(): array
    {
        return $this->students;
    }

    public function getDayCount()
    {
        return $this->dayCount;
    }




}