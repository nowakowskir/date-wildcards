<?php

namespace Nowakowskir\DateWildcards;

use Illuminate\Support\Collection;

class DateWildcards
{

    protected Carbon $fromDate;
    protected Carbon $toDate;

    public function __construct(Carbon $fromDate, Carbon $toDate)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function getDates($yearPattern, $monthPattern, $dayPattern, $weekDayPattern): Collection
    {
        $yearParser = new YearParser((int) $this->fromDate->format('Y'), (int) $this->toDate->format('Y'));
        $years = $yearParser->parse($yearPattern);

        $dates = new Collection([]);

        if ($years->isEmpty()) {
            return $dates;
        }

        $monthParser = new MonthParser();
        $months = $monthParser->parse($monthPattern);

        if ($months->isEmpty()) {
            return $dates;
        }

        $dayParser = new DayParser();
        $weekDayParser = new WeekdayParser();
        $weekDays = $weekDayParser->parse($weekDayPattern)->toArray();

        $years->each(function ($year) use ($dayParser, $weekDays, $months, $dayPattern, &$dates) {
            $months->each(function ($month) use ($year, $dayParser, $weekDays, $months, $dayPattern, &$dates) {
                $days = $dayParser->parse($year, $month, $dayPattern);

                if (! $days->isEmpty()) {
                    $days->each(function ($day) use ($year, $month, $weekDays, &$dates) {
                        $dateAsString = sprintf('%d-%02d-%02d', $year, $month, $day);
                        $date = Carbon::createFromFormat('Y-m-d', $dateAsString);

                        if ($date
                            && in_array((int) $date->format('N'), $weekDays)
                            && $this->isBetween((int) $date->timestamp, (int) $this->fromDate->timestamp, (int) $this->toDate->timestamp)) {

                            $dates->add($dateAsString);
                        }
                    });
                }
            });
        });

        return $dates
            ->unique()
            ->sort()
            ->values();
    }

    protected function isBetween(int $value, int $lowerLimit, int $upperLimit): bool
    {
        return $value >= $lowerLimit && $value <= $upperLimit;
    }
}
