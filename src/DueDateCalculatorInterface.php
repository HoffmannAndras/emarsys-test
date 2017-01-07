<?php

namespace Emarsys;

interface DueDateCalculatorInterface
{
    public function calculateDueDate(\DateTime $dueDate, int $estimate): \DateTime;
}