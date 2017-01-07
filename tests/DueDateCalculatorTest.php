<?php

namespace Emarsys\Tests;

use Emarsys\DueDateCalculator;
use Emarsys\DueDateCalculatorInterface;

class DueDateCalculatorTest extends \PHPUnit_Framework_TestCase
{
    public function testInstance()
    {
        $calculator = $this->newCalculator(9, 17);
        $this->assertInstanceOf(DueDateCalculatorInterface::class, $calculator);
    }

    /**
     * @expectedException \Emarsys\CalculatorException
     * @expectedExceptionMessage Invalid submit time [2017-01-06 17:00:00], it should be between 9 and 17 on workdays
     */
    public function testInvalidSubmitHourException()
    {
        $this->newCalculator(9, 17)->calculateDueDate(new \DateTime('2017-01-06 17:00:00'), 1);
    }

    /**
     * @expectedException \Emarsys\CalculatorException
     * @expectedExceptionMessage Invalid [-5] estimated time
     */
    public function testInvalidEstimateException()
    {
        $this->newCalculator(9, 17)->calculateDueDate(new \DateTime('2017-01-06 09:00:00'), -5);
    }

    /**
     * @dataProvider provider
     */
    public function testCalculateDueDate(string $expectedDate, string $submitDate, int $estimate)
    {
        $dueDate = $this->newCalculator(9, 17)->calculateDueDate(new \DateTime($submitDate), $estimate);
        $this->assertEquals(new \DateTime($expectedDate), $dueDate);
    }

    public function provider()
    {
        return [
            ['2017-01-06 10:00:00', '2017-01-06 09:00:00', 1],
            ['2017-01-06 10:00:00', '2017-01-05 16:00:00', 2],
            ['2017-01-09 10:00:00', '2017-01-05 16:00:00', 10],
            ['2017-01-06 10:00:00', '2017-01-05 16:00:00', 2],
            ['2017-01-16 13:00:00', '2017-01-06 16:00:00', 45],
            ['2017-01-09 09:01:01', '2017-01-06 16:01:01', 1],
            ['2017-02-07 10:01:01', '2017-01-06 16:01:01', 170]
        ];
    }

    private function newCalculator(int $minSubmitHour, int $maxSubmitHour): DueDateCalculator
    {
        return new DueDateCalculator($minSubmitHour, $maxSubmitHour);
    }
}
