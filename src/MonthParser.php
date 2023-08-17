<?php

namespace Nowakowskir\DateWildcards;

class MonthParser
{

    public function parse(string $value): array
    {
        $collection = [];

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

    protected function parseSingleValue(string $value, array $collection): array
    {
        $collection[] = (int) $value;

        return $collection;
    }

    protected function parseMultipleValues(array $matches, array $collection): array
    {
        foreach (($matches[0] ?? []) as $match) {
            $collection[] = (int) $match;
        }

        $collection = array_unique($collection);
        asort($collection);

        return $collection;
    }

    public function fillMonthsInRange(int $fromMonth, int $toMonth, array $collection): array
    {
        for ($m = $fromMonth; $m <= $toMonth; $m++) {
            $collection[] = $m;
        }

        $collection = array_unique($collection);
        asort($collection);

        return $collection;
    }
}