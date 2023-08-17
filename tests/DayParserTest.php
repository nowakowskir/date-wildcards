<?php

namespace Tests\Unit;

use Nowakowskir\DateWildcards\DayParser;
use PHPUnit\Framework\TestCase;

class DayParserTest extends TestCase
{

    public function test_open_single_value()
    {
        $dayParser = new DayParser();
        $daysCollection = $dayParser->parse(2022, 2, '*');

        $a = [];
        for ($i = 1; $i <= 28; $i++) {
            $a[] = $i;
        }

        $this->assertCount(count($a), $daysCollection->toArray());
        $this->assertEquals($a, $daysCollection->toArray());
    }

    public function test_single_value()
    {
        $dayParser = new DayParser();
        $daysCollection = $dayParser->parse(2022, 2, '12');

        $this->assertCount(1, $daysCollection->toArray());
        $this->assertEquals([12], $daysCollection->toArray());
    }

    public function test_single_value_returns_empty_collection_if_day_exceeds_number_of_days_in_month()
    {
        $dayParser = new DayParser();

        $daysCollection = $dayParser->parse(2022, 2, '29');
        $this->assertCount(0, $daysCollection->toArray());
    }

    public function test_single_negative_zero_value()
    {
        $dayParser = new DayParser();
        $daysCollection = $dayParser->parse(2022, 2, '-0');

        $this->assertCount(1, $daysCollection->toArray());
        $this->assertEquals([28], $daysCollection->toArray());
    }

    public function test_single_negative_non_zero_value()
    {
        $dayParser = new DayParser();
        $daysCollection = $dayParser->parse(2022, 2, '-1');

        $this->assertCount(1, $daysCollection->toArray());
        $this->assertEquals([27], $daysCollection->toArray());
    }

    public function test_single_negative_value_returns_empty_collection_if_value_exceeds_number_of_days_in_month()
    {
        $dayParser = new DayParser();
        $daysCollection = $dayParser->parse(2022, 2, '-29');

        $this->assertCount(0, $daysCollection->toArray());
    }

    public function test_two_values_comma_delimited()
    {
        $dayParser = new DayParser();
        $daysCollection = $dayParser->parse(2022, 2, '11,12');

        $this->assertCount(2, $daysCollection->toArray());
        $this->assertEquals([11, 12], $daysCollection->toArray());
    }

    public function test_two_values_comma_delimited_skips_days_which_exceed_number_of_days_in_month()
    {
        $dayParser = new DayParser();

        $daysCollection = $dayParser->parse(2022, 2, '11,29');
        $this->assertCount(1, $daysCollection->toArray());
        $this->assertEquals([11], $daysCollection->toArray());
    }

    public function test_two_values_comma_delimited_with_negative_zero_value()
    {
        $dayParser = new DayParser();
        $daysCollection = $dayParser->parse(2022, 2, '11,-0');

        $this->assertCount(2, $daysCollection->toArray());
        $this->assertEquals([11, 28], $daysCollection->toArray());
    }

    public function test_two_values_comma_delimited_with_negative_non_zero_value()
    {
        $dayParser = new DayParser();
        $daysCollection = $dayParser->parse(2022, 2, '11,-1');

        $this->assertCount(2, $daysCollection->toArray());
        $this->assertEquals([11, 27], $daysCollection->toArray());
    }

    public function test_two_values_comma_delimited_with_negative_value_skips_days_if_negative_value_exceeds_number_of_days_in_month()
    {
        $dayParser = new DayParser();
        $daysCollection = $dayParser->parse(2022, 2, '11,-29');

        $this->assertCount(1, $daysCollection->toArray());
        $this->assertEquals([11], $daysCollection->toArray());
    }

    public function test_multiple_values_comma_delimited()
    {
        $dayParser = new DayParser();
        $daysCollection = $dayParser->parse(2022, 2, '10,11,12');

        $this->assertCount(3, $daysCollection->toArray());
        $this->assertEquals([10, 11, 12], $daysCollection->toArray());
    }

    public function test_multiple_values_comma_delimited_sorts_values()
    {
        $dayParser = new DayParser();
        $daysCollection = $dayParser->parse(2022, 2, '12,11,13,9');

        $this->assertCount(4, $daysCollection->toArray());
        $this->assertEquals([9, 11, 12, 13], $daysCollection->toArray());
    }

    public function test_multiple_values_comma_delimited_deletes_duplicated_values()
    {
        $dayParser = new DayParser();
        $daysCollection = $dayParser->parse(2022, 2, '12,11,11,12,13,9');

        $this->assertCount(4, $daysCollection->toArray());
        $this->assertEquals([9, 11, 12, 13], $daysCollection->toArray());
    }

    public function test_multiple_values_comma_delimited_skips_days_which_exceed_number_of_days_in_month()
    {
        $dayParser = new DayParser();

        $daysCollection = $dayParser->parse(2022, 2, '10,29,12');
        $this->assertCount(2, $daysCollection->toArray());
        $this->assertEquals([10, 12], $daysCollection->toArray());
    }

    public function test_multiple_values_comma_delimited_with_negative_non_zero_value()
    {
        $dayParser = new DayParser();
        $daysCollection = $dayParser->parse(2022, 2, '11,-2,-1');

        $this->assertCount(3, $daysCollection->toArray());
        $this->assertEquals([11, 26, 27], $daysCollection->toArray());
    }

    public function test_multiple_values_comma_delimited_with_negative_value_skips_days_if_negative_value_exceeds_number_of_days_in_month()
    {
        $dayParser = new DayParser();
        $daysCollection = $dayParser->parse(2022, 2, '11,-30,-29');

        $this->assertCount(1, $daysCollection->toArray());
        $this->assertEquals([11], $daysCollection->toArray());
    }

    public function test_open_range_with_start_value()
    {
        $dayParser = new DayParser();
        $daysCollection = $dayParser->parse(2022, 2, '22-*');

        $a = [22, 23, 24, 25, 26, 27, 28];

        $this->assertCount(count($a), $daysCollection->toArray());
        $this->assertEquals($a, $daysCollection->toArray());
    }

    public function test_open_range_with_negative_zero_start_value()
    {
        $dayParser = new DayParser();
        $daysCollection = $dayParser->parse(2022, 2, '-0-*');

        $this->assertCount(1, $daysCollection->toArray());
    }

    public function test_open_range_with_negative_non_zero_start_value()
    {
        $dayParser = new DayParser();
        $daysCollection = $dayParser->parse(2022, 2, '-2-*');

        $this->assertCount(3, $daysCollection->toArray());
        $this->assertEquals([26, 27, 28], $daysCollection->toArray());
    }

    public function test_open_range_with_start_value_returns_empty_collection_if_start_day_exceeds_number_of_days_in_month()
    {
        $dayParser = new DayParser();

        $daysCollection = $dayParser->parse(2022, 2, '29-*');
        $this->assertCount(0, $daysCollection->toArray());
    }

    public function test_open_range_with_with_negative_start_value_skips_days_which_exceed_number_of_days_in_month()
    {
        $dayParser = new DayParser();

        $daysCollection = $dayParser->parse(2022, 2, '-29-*');

        $a = [];
        for ($i = 1; $i <= 28; $i++) {
            $a[] = $i;
        }

        $this->assertCount(count($a), $daysCollection->toArray());
        $this->assertEquals($a, $daysCollection->toArray());
    }

    public function test_open_range_with_end_value()
    {
        $dayParser = new DayParser();
        $daysCollection = $dayParser->parse(2022, 2, '*-5');

        $this->assertCount(5, $daysCollection->toArray());
        $this->assertEquals([
            1,
            2,
            3,
            4,
            5
        ], $daysCollection->toArray());
    }

    public function test_open_range_with_end_value_skips_days_which_exceed_number_of_days_in_month()
    {
        $dayParser = new DayParser();

        $daysCollection = $dayParser->parse(2022, 2, '*-29');

        $a = [];
        for ($i = 1; $i <= 28; $i++) {
            $a[] = $i;
        }

        $this->assertCount(count($a), $daysCollection->toArray());
        $this->assertEquals($a, $daysCollection->toArray());
    }

    public function test_open_range_with_start_and_end_values()
    {
        $dayParser = new DayParser();
        $daysCollection = $dayParser->parse(2022, 2, '4-8');

        $this->assertCount(5, $daysCollection->toArray());
        $this->assertEquals([
            4,
            5,
            6,
            7,
            8
        ], $daysCollection->toArray());
    }

    public function test_open_range_with_start_and_end_values_returns_empty_collection_if_start_day_exceeds_number_of_days_in_month()
    {
        $dayParser = new DayParser();

        $daysCollection = $dayParser->parse(2022, 2, '29-31');
        $this->assertCount(0, $daysCollection->toArray());
    }

    public function test_open_range_with_start_and_end_values_skips_days_which_exceed_number_of_days_in_month()
    {
        $dayParser = new DayParser();

        $daysCollection = $dayParser->parse(2022, 2, '24-29');

        $a = [24, 25, 26, 27, 28];

        $this->assertCount(count($a), $daysCollection->toArray());
        $this->assertEquals($a, $daysCollection->toArray());
    }
}
