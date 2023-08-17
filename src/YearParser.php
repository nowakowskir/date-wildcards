<?php

namespace Nowakowskir\DateWildcards;

class YearParser
{

    protected int $lowerLimit = 1970;
    protected int $upperLimit = 2099;

    public function __construct(?int $lowerLimit = null, ?int $upperLimit = null)
    {
        if ($lowerLimit) {
            $this->lowerLimit = $lowerLimit;
        }

        if ($upperLimit) {
            $this->upperLimit = $upperLimit;
        }
    }

    public function parse(string $value): array
    {
        $collection = [];

        $matches = null;

        if (preg_match('/^\*(?:\/(\d+))?$/', $value, $matches) === 1) {
            $collection = $this->fillYearsInRange($this->lowerLimit, $this->upperLimit, array_key_exists(1, $matches) ? (int) $matches[1] : null, $collection);
        } elseif (preg_match('/^(\d+)$/', $value, $matches) === 1) {
            $collection = $this->parseSingleValue($value, $collection);
        } elseif (preg_match('/^(\d+)\-\*(?:\/(\d+))?$/', $value, $matches) === 1) {
            $collection = $this->fillYearsInRange($this->getHighestValue((int) $matches[1], $this->lowerLimit), $this->upperLimit, array_key_exists(2, $matches) ? (int) $matches[2] : null, $collection);
        } elseif (preg_match('/^\*\-(\d+)(?:\/(\d+))?$/', $value, $matches) === 1) {
            $collection = $this->fillYearsInRange($this->lowerLimit, $this->getLowerValue((int) $matches[1], $this->upperLimit), array_key_exists(2, $matches) ? (int) $matches[2] : null, $collection);
        } elseif (preg_match('/^(\d+)\-(\d+)(?:\/(\d+))?$/', $value, $matches) === 1) {
            $collection = $this->fillYearsInRange($this->getHighestValue((int) $matches[1], $this->lowerLimit), $this->getLowerValue((int) $matches[2], $this->upperLimit), array_key_exists(3, $matches) ? (int) $matches[3] : null, $collection);
        } elseif (! in_array(preg_match_all('/(\d+)+(?=[,]?)/', $value, $matches), [0, false])) {
            $collection = $this->parseMultipleValues($matches, $collection);
        }

        return $collection;
    }

    protected function getHighestValue(int $value1, int $value2): int
    {
        if ($value1 > $value2) {
            return $value1;
        } elseif ($value1 < $value2) {
            return $value2;
        }

        return $value1;
    }

    protected function getLowerValue(int $value1, int $value2): int
    {
        if ($value1 > $value2) {
            return $value2;
        } elseif ($value1 < $value2) {
            return $value1;
        }

        return $value1;
    }

    protected function parseSingleValue(string $value, array $collection): array
    {
        $value = (int) $value;
        if ($this->isBetween($value, $this->lowerLimit, $this->upperLimit)) {
            $collection[] = $value;
        }

        return $collection;
    }

    protected function parseMultipleValues(array $matches, array $collection): array
    {
        foreach (($matches[0] ?? []) as $match) {
            $value = (int) $match;
            if ($this->isBetween($value, $this->lowerLimit, $this->upperLimit)) {
                $collection[] = (int) $match;
            }
        }

        $collection = array_unique($collection);
        asort($collection);

        return $collection;
    }

    public function isBetween(int $value, int $lowerLimit, int $upperLimit)
    {
        return $value >= $lowerLimit && $value <= $upperLimit;
    }

    public function fillYearsInRange(int $fromYear, int $toYear, ?int $everyNth, array $collection): array
    {
        $i = 0;
        for ($y = $fromYear; $y <= $toYear; $y++) {
            if (! is_null($everyNth) && $everyNth > 0) {
                if ($i === 0 || $i % $everyNth === 0) {
                    $collection[] = $y;
                }

                $i++;
            } else {
                $collection[] = $y;
            }
        }

        $collection = array_unique($collection);
        asort($collection);

        return $collection;
    }
}