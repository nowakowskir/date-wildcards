<?php

namespace Tests\Unit;

use Nowakowskir\DateWildcards\YearParser;
use PHPUnit\Framework\TestCase;

class YearParserTest extends TestCase
{
    public function test_open_single_value()
    {
        $yearParser = new YearParser();
        $yearsCollection = $yearParser->parse('*');

        $a = [];
        for ($i = 1970; $i <= 2099; $i++) {
            $a[] = $i;
        }

        $this->assertCount(count($a), $yearsCollection);
        $this->assertEquals($a, $yearsCollection);
    }

    public function test_open_single_value_with_every_nth()
    {
        $yearParser = new YearParser();
        $yearsCollection = $yearParser->parse('*/2');

        $a = [];
        for ($i = 1970; $i <= 2099; $i++) {
            if ($i % 2 === 0) {
                $a[] = $i;
            }
        }

        $this->assertCount(count($a), $yearsCollection);
        $this->assertEquals($a, $yearsCollection);
    }

    public function test_single_value()
    {
        $yearParser = new YearParser();
        $yearsCollection = $yearParser->parse('2022');

        $this->assertCount(1, $yearsCollection);
        $this->assertEquals([2022], $yearsCollection);
    }

    public function test_two_values_comma_delimited()
    {
        $yearParser = new YearParser();
        $yearsCollection = $yearParser->parse('2022,2023');

        $this->assertCount(2, $yearsCollection);
        $this->assertEquals([2022, 2023], $yearsCollection);
    }

    public function test_multiple_values_comma_delimited()
    {
        $yearParser = new YearParser();
        $yearsCollection = $yearParser->parse('2022,2023,2024');

        $this->assertCount(3, $yearsCollection);
        $this->assertEquals([2022, 2023, 2024], $yearsCollection);
    }

    public function test_open_range_with_start_value()
    {
        $yearParser = new YearParser();
        $yearsCollection = $yearParser->parse('2090-*');

        $this->assertCount(10, $yearsCollection);
        $this->assertEquals([
            2090,
            2091,
            2092,
            2093,
            2094,
            2095,
            2096,
            2097,
            2098,
            2099
        ], $yearsCollection);
    }

    public function test_open_range_with_start_value_and_every_nth()
    {
        $yearParser = new YearParser();
        $yearsCollection = $yearParser->parse('2090-*/2');

        $this->assertCount(5, $yearsCollection);
        $this->assertEquals([
            2090,
            2092,
            2094,
            2096,
            2098,
        ], $yearsCollection);
    }

    public function test_open_range_with_end_value()
    {
        $yearParser = new YearParser();
        $yearsCollection = $yearParser->parse('*-2009');

        $a = [];
        for ($i = 1970; $i <= 2009; $i++) {
            $a[] = $i;
        }

        $this->assertCount(count($a), $yearsCollection);
        $this->assertEquals($a, $yearsCollection);
    }

    public function test_open_range_with_end_value_and_every_nth()
    {
        $yearParser = new YearParser();
        $yearsCollection = $yearParser->parse('*-2009/2');

        $a = [];
        for ($i = 1970; $i <= 2009; $i++) {
            if ($i % 2 === 0) {
                $a[] = $i;
            }
        }

        $this->assertCount(count($a), $yearsCollection);
        $this->assertEquals($a, $yearsCollection);
    }

    public function test_open_range_with_start_and_end_values()
    {
        $yearParser = new YearParser();
        $yearsCollection = $yearParser->parse('2090-2095');

        $this->assertCount(6, $yearsCollection);
        $this->assertEquals([
            2090,
            2091,
            2092,
            2093,
            2094,
            2095,
        ], $yearsCollection);
    }

    public function test_open_range_with_start_and_end_values_and_every_nth()
    {
        $yearParser = new YearParser();
        $yearsCollection = $yearParser->parse('2090-2099/2');

        $this->assertCount(5, $yearsCollection);
        $this->assertEquals([
            2090,
            2092,
            2094,
            2096,
            2098,
        ], $yearsCollection);
    }

    public function test_open_range_with_start_and_end_values_and_every_nth_case_2()
    {
        $yearParser = new YearParser();
        $yearsCollection = $yearParser->parse('2091-2098/2');

        $this->assertCount(4, $yearsCollection);
        $this->assertEquals([
            2091,
            2093,
            2095,
            2097,
        ], $yearsCollection);
    }

    public function test_open_range_limited_to_provided_limits()
    {
        $yearParser = new YearParser(2000, 2002);
        $yearsCollection = $yearParser->parse('*');

        $this->assertCount(3, $yearsCollection);
        $this->assertEquals([
            2000,
            2001,
            2002
        ], $yearsCollection);
    }

    public function test_open_range_with_start_value_limited_to_provided_limits()
    {
        $yearParser = new YearParser(2000, 2002);
        $yearsCollection = $yearParser->parse('2000-*');

        $this->assertCount(3, $yearsCollection);
        $this->assertEquals([
            2000,
            2001,
            2002
        ], $yearsCollection);
    }

    public function test_open_range_with_end_value_limited_to_provided_limits()
    {
        $yearParser = new YearParser(2000, 2002);
        $yearsCollection = $yearParser->parse('*-2010');

        $this->assertCount(3, $yearsCollection);
        $this->assertEquals([
            2000,
            2001,
            2002
        ], $yearsCollection);
    }

    public function test_open_range_with_start_and_end_value_limited_to_provided_limits()
    {
        $yearParser = new YearParser(2000, 2002);
        $yearsCollection = $yearParser->parse('1955-2010');

        $this->assertCount(3, $yearsCollection);
        $this->assertEquals([
            2000,
            2001,
            2002
        ], $yearsCollection);
    }

    public function test_multiple_values_limited_to_provided_limits()
    {
        $yearParser = new YearParser(2000, 2002);
        $yearsCollection = $yearParser->parse('1999,2000,2003,2002,2005');

        $this->assertCount(2, $yearsCollection);
        $this->assertEquals([
            2000,
            2002
        ], $yearsCollection);
    }

    public function test_single_value_within_provided_limits()
    {
        $yearParser = new YearParser(2000, 2002);
        $yearsCollection = $yearParser->parse('2002');

        $this->assertCount(1, $yearsCollection);
        $this->assertEquals([
            2002
        ], $yearsCollection);
    }

    public function test_single_value_out_of_provided_limit()
    {
        $yearParser = new YearParser(2000, 2002);
        $yearsCollection = $yearParser->parse('2003');

        $this->assertCount(0, $yearsCollection);
    }
}
