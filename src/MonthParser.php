<?php

namespace Nowakowskir\DateWildcards;

use Illuminate\Support\Collection;

class MonthParser
{

    public function parse(string $value): Collection
    {
        $collection = collect([]);

        $matches = null;

        if (preg_match('/^\*$/', $value, $matches) === 1) {
            $collection = $this->fillMonthsInRange(1, 12, $collection);
        } elseif (preg_match('/^(\d+)$/', $value, $matches) === 1) {
            $collection = $this->parseSingleValue($value, $collection);
        } elseif (preg_match('/^(\d+)\-\*?$/', $value, $matches) === 1) {
            $collection = $this->fillMonthsInRange((int) $matches[1], 12, $collection);
        } elseif (preg_match('/^\*\-(\d+)?$/', $value, $matches) === 1) {
            $collection = $this->fillMonthsInRange(1, (int) $matches[1], $collection);
        } elseif (preg_match('/^(\d+)\-(\d+)?$/', $value, $matches) === 1) {
            $collection = $this->fillMonthsInRange((int) $matches[1], (int) $matches[2], $collection);
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

    public function fillMonthsInRange(int $fromMonth, int $toMonth, Collection $collection)
    {
        for ($m = $fromMonth; $m <= $toMonth; $m++) {
            $collection->add($m);
        }

        return $collection->unique()->sort();
    }
}