<?php

namespace Tests\Unit;

use Nowakowskir\DateWildcards\WeekdayParser;
use PHPUnit\Framework\TestCase;

class WeekdayParserTest extends TestCase
{
    public function test_open_single_value()
    {
        $weekDayParser = new WeekDayParser();
        $weekDaysCollection = $weekDayParser->parse('*');

        $a = [];
        for ($i = 1; $i <= 7; $i++) {
            $a[] = $i;
        }

        $this->assertCount(count($a), $weekDaysCollection);
        $this->assertEquals($a, $weekDaysCollection);
    }

    public function test_single_value()
    {
        $weekDayParser = new WeekDayParser();
        $weekDaysCollection = $weekDayParser->parse('6');

        $this->assertCount(1, $weekDaysCollection);
        $this->assertEquals([6], $weekDaysCollection);
    }

    public function test_two_values_comma_delimited()
    {
        $weekDayParser = new WeekDayParser();
        $weekDaysCollection = $weekDayParser->parse('5,6');

        $this->assertCount(2, $weekDaysCollection);
        $this->assertEquals([5, 6], $weekDaysCollection);
    }

    public function test_multiple_values_comma_delimited()
    {
        $weekDayParser = new WeekDayParser();
        $weekDaysCollection = $weekDayParser->parse('1,2,3');

        $this->assertCount(3, $weekDaysCollection);
        $this->assertEquals([1, 2, 3], $weekDaysCollection);
    }

    public function test_open_range_with_start_value()
    {
        $weekDayParser = new WeekDayParser();
        $weekDaysCollection = $weekDayParser->parse('3-*');

        $this->assertCount(5, $weekDaysCollection);
        $this->assertEquals([
            3,
            4,
            5,
            6,
            7
        ], $weekDaysCollection);
    }

    public function test_open_range_with_end_value()
    {
        $weekDayParser = new WeekDayParser();
        $weekDaysCollection = $weekDayParser->parse('*-5');

        $this->assertCount(5, $weekDaysCollection);
        $this->assertEquals([
            1,
            2,
            3,
            4,
            5
        ], $weekDaysCollection);
    }

    public function test_open_range_with_start_and_end_values()
    {
        $weekDayParser = new WeekDayParser();
        $weekDaysCollection = $weekDayParser->parse('3-5');

        $this->assertCount(3, $weekDaysCollection);
        $this->assertEquals([
            3,
            4,
            5
        ], $weekDaysCollection);
    }
}
