<?php

namespace Nowakowskir\DateWildcards;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class DayParser
{

    public function parse(int $year, int $month, string $value): Collection
    {
        $collection = collect([]);

        $refDate = Carbon::createFromFormat('Y-n-j', sprintf('%d-%d-1', $year, $month));
        $daysInMonth = $refDate->daysInMonth;

        $matches = null;
        if (preg_match('/^\*$/', $value, $matches) === 1) {
            $collection = $this->fillDaysInRange(1, (string)$daysInMonth, $daysInMonth, $collection);
        } elseif (preg_match('/^(-?\d+)$/', $value, $matches) === 1) {
            // Single value: 2
            $collection = $this->parseSingleValue($value, $daysInMonth, $collection);
        } elseif (preg_match('/^(-?\d+)\-\*?$/', $value, $matches) === 1) {
            // Range: 10-* or 10-*/2, 10-*/3, etc.
            $collection = $this->fillDaysInRange($matches[1], (string) $daysInMonth, $daysInMonth, $collection);
        } elseif (preg_match('/^\*\-(-?\d+)?$/', $value, $matches) === 1) {
            // Range: *-30 or *-30/2, *-30/3, etc.
            $collection = $this->fillDaysInRange(1, (string) $matches[1], $daysInMonth, $collection);
        } elseif (preg_match('/^(-?\d+)\-(-?\d+)?$/', $value, $matches) === 1) {
            // Range: 1-30 or 1-30/2, 1-30/3, etc.
            $collection = $this->fillDaysInRange($matches[1], (string) $matches[2], $daysInMonth, $collection);
        } elseif (!in_array(preg_match_all('/(-?\d)+(?=[,]?)/', $value, $matches), [0, false])) {
            // Multiple comma-delimited values: 2,3,4,5
            $collection = $this->parseMultipleValues($matches, $daysInMonth, $collection);
        }

        return $collection;
    }

    protected function parseSingleValue(string $value, int $daysInMonth, Collection $collection): Collection
    {
        $day = (int) $value;

        if ($day <= 0) {
            $day = $daysInMonth - abs($day);
            if ($day >= 1) {
                $collection->add($day);
            }
        } elseif ($day <= $daysInMonth) {
            $collection->add($day);
        }

        return $collection;
    }

    protected function parseMultipleValues(array $matches, int $daysInMonth, Collection $collection): Collection
    {
        foreach (($matches[0] ?? []) as $match) {
            $day = (int) $match;

            if ($day <= 0) {
                $day = $daysInMonth - abs($day);
                if ($day >= 1) {
                    $collection->add($day);
                }
            } elseif ($day <= $daysInMonth) {
                $collection->add($day);
            }
        }

        return $collection->unique()->sort()->values();
    }

    public function fillDaysInRange(string $fromDay, string $toDay, int $daysInMonth, Collection $collection)
    {
        if ($fromDay === '-0') {
            $fromDay = $daysInMonth;
        } elseif ((int) $fromDay < 1) {
            $fromDay = $daysInMonth - abs((int)$fromDay);
            if ($fromDay < 1) {
                $fromDay = 1;
            }
        } else {
            $fromDay = (int) $fromDay;
        }

        for ($d = $fromDay; $d <= $toDay; $d++) {
            if ($d <= $daysInMonth) {
                $collection->add($d);
            }
        }

        return $collection->unique()
            ->sort()
            ->values();
    }
}