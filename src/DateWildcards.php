<?php

namespace Nowakowskir\DateWildcards;

use Carbon\Carbon;

class DateWildcards
{

    protected Carbon $fromDate;
    protected Carbon $toDate;

    public function __construct(Carbon $fromDate, Carbon $toDate)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function getDates($yearPattern, $monthPattern, $dayPattern, $weekDayPattern): array
    {
        $yearParser = new YearParser((int) $this->fromDate->format('Y'), (int) $this->toDate->format('Y'));
        $years = $yearParser->parse($yearPattern);

        $dates = [];

        if (empty($years)) {
            return $dates;
        }

        $monthParser = new MonthParser();
        $months = $monthParser->parse($monthPattern);

        if (empty($months)) {
            return $dates;
        }

        $dayParser = new DayParser();
        $weekDayParser = new WeekdayParser();
        $weekDays = $weekDayParser->parse($weekDayPattern)->toArray();

        foreach ($years as $year) {
            foreach ($months as $month) {
                $days = $dayParser->parse($year, $month, $dayPattern);
                if (! empty($days)) {
                    foreach ($days as $day) {
                        $dateAsString = sprintf('%d-%02d-%02d', $year, $month, $day);
                        $date = Carbon::createFromFormat('Y-m-d', $dateAsString);

                        if ($date
                            && in_array((int)$date->format('N'), $weekDays)
                            && $this->isBetween((int)$date->timestamp, (int)$this->fromDate->timestamp, (int)$this->toDate->timestamp)) {

                            $dates[] = $dateAsString;
                        }
                    }
                }
            }
        }

        $dates = array_unique($dates);
        asort($dates);

        return $dates;
    }

    protected function isBetween(int $value, int $lowerLimit, int $upperLimit): bool
    {
        return $value >= $lowerLimit && $value <= $upperLimit;
    }
}
