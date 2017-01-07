<?php

namespace Emarsys;

class DueDateCalculator implements DueDateCalculatorInterface
{
    private $minSubmitHour;
    private $maxSubmitHour;
    private $workingHours;

    public function __construct(int $minSubmitHour, int $maxSubmitHour)
    {
        $this->minSubmitHour = $minSubmitHour;
        $this->maxSubmitHour = $maxSubmitHour;
        $this->workingHours = $maxSubmitHour - $minSubmitHour;
    }

    public function calculateDueDate(\DateTime $submitDate, int $estimate): \DateTime
    {
        $dueDate = clone $submitDate;
        $submitHour = (int)$dueDate->format('H');

        $this->checkParameters($submitDate, $estimate, $submitHour);

        $this->addEstimate($dueDate, $estimate, $submitHour);

        return $dueDate;
    }

    private function checkParameters(\DateTime $submitDate, int $estimate, int $submitHour)
    {
        if (!$this->isValidSubmitHour($submitHour) || $this->isWeekend($submitDate))
            throw CalculatorException::createInvalidSubmitHour($submitDate, $this->minSubmitHour, $this->maxSubmitHour);

        if ($estimate <= 0)
            throw CalculatorException::createInvalidEstimate($estimate);
    }

    private function isValidSubmitHour(int $hour): bool
    {
        return $hour >= $this->minSubmitHour && $hour < $this->maxSubmitHour;
    }

    private function isWeekend(\DateTime $dueDate): bool
    {
        return !in_array($dueDate->format('N'), range(1, 5));
    }

    private function addEstimate(\DateTime $dueDate, int $estimate, $submitHour)
    {
        $numberOfFullDays = floor($estimate / $this->workingHours);

        $this->addWeekdays($dueDate, $numberOfFullDays);
        $estimateHours = $submitHour + ($estimate % $this->workingHours);

        if ($estimateHours >= $this->maxSubmitHour) {
            $this->addWeekdays($dueDate, 1);
            $restHours = $this->workingHours + ($estimateHours % $this->workingHours);
        } else {
            $restHours = $estimateHours;
        }

        $this->setTime($dueDate, $restHours);
    }

    private function setTime(\DateTime $dueDate, int $estimateHours)
    {
        $dueDate->setTime($estimateHours, $dueDate->format('i'), $dueDate->format('s'));
    }

    private function addWeekdays(\DateTime $dueDate, int $days)
    {
        $dueDate->add(\DateInterval::createFromDateString($days . ' weekday'));
    }
}
