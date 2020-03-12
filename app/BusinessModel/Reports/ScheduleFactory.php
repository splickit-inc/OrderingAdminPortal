<?php

namespace App\BusinessModel\Reports;



class ScheduleFactory
{
    /**
     * @param $frequency
     * @return int
     */
    static function getScheduleNextDateByFrequency($frequency)
    {
        try {
            //The hours is was thinking to be set on America/Denver Timezone MTC
            // the worker is set to run at 4am
            switch ($frequency) {
                case 'daily':
                    return strtotime('next day 03am');
                    break;
                case 'weekly':
                    return strtotime('next monday 03am');
                    break;
                case 'monthly':
                    return strtotime('first day of next month 03am');
                    break;
                default:
                    return 0;
                    break;
            }
        } catch (\Exception $exception) {
            return 0;
        }
    }
}