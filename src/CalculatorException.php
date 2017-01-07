<?php

namespace Emarsys;

class CalculatorException extends \LogicException
{
    public static function createInvalidEstimate(int $estimatedTime): CalculatorException
    {
        return new static(
            sprintf(
                "Invalid [%s] estimated time",
                $estimatedTime
            )
        );
    }

    public static function createInvalidSubmitHour(\DateTime $submitDate, int $minSubmitHour, int $maxSubmitHour): CalculatorException
    {
        return new static(
            sprintf( "Invalid submit time [%s], it should be between %s and %s on workdays",
                $submitDate->format("Y-m-d H:i:s"),
                $minSubmitHour,
                $maxSubmitHour
            )
        );
    }
}