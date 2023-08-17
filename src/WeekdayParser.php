<?php

namespace Nowakowskir\DateWildcards;

use Illuminate\Support\Collection;

class WeekdayParser
{

    public function parse(string $value): Collection
    {
        $collection = collect([]);

        $matches = null;

        if (preg_match('/^\*$/', $value, $matches) === 1) {
            $collection = $this->fillWeekDaysInRange(1, 7, $collection);
        } elseif (preg_match('/^(\d+)$/', $value, $matches) === 1) {
            $collection = $this->parseSingleValue($value, $collection);
        } elseif (preg_match('/^(\d+)\-\*$/', $value, $matches) === 1) {
            $collection = $this->fillWeekDaysInRange((int) $matches[1], 7, $collection);
        } elseif (preg_match('/^\*\-(\d+)$/', $value, $matches) === 1) {
            $collection = $this->fillWeekDaysInRange(1, (int) $matches[1], $collection);
        } elseif (preg_match('/^(\d+)\-(\d+)$/', $value, $matches) === 1) {
            $collection = $this->fillWeekDaysInRange((int) $matches[1], (int) $matches[2], $collection);
        } elseif (! in_array(preg_match_all('/(\d)+(?=[,]?)/', $value, $matches), [0, false])) {
            $collection = $this->parseMultipleValues($matches, $collection);
        }

        return $collection;
    }

    protected function parseSingleValue(string $value, Collection $collection): Collection
    {
        $collection->add((int) $value);

        return $collection;
    }

    protected function parseMultipleValues(array $matches, Collection $collection): Collection
    {
        foreach (($matches[0] ?? []) as $match) {
            $collection->add((int) $match);
        }

        return $collection->unique()->sort();
    }

    public function fillWeekDaysInRange(int $fromWeekDay, int $toWeekDay, Collection $collection): Collection
    {
        for ($w = $fromWeekDay; $w <= $toWeekDay; $w++) {
            $collection->add($w);
        }

        return $collection->unique()->sort();
    }
}