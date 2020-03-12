<?php

namespace App\Model;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Finder\Comparator\DateComparator;

class DeviceCallInHistory extends Model
{
    protected $table = 'device_call_in_history';

    protected $appends = ['time_zone_applied'];

    function merchant()
    {
        return $this->hasOne(Merchant::class, 'merchant_id', 'merchant_id');
    }


    public function getLastCallInAttribute($last_call_tz_datetime)
    {
        $last_call_tz_datetime = new \DateTime();
        $last_call_tz_datetime->setTimeStamp($this->last_call_in_as_integer);

        if (!empty($this->merchant) && !empty($this->merchant->time_zone_string)) {
            $merchant_timezone = new \DateTimeZone($this->merchant->time_zone_string);
            $merchant_timezone = new \DateTimeZone('(GMT-8:00) America/Los_Angeles (US & Canada)');
            $last_call_tz_datetime->setTimezone($merchant_timezone);
            $last_call_tz_datetime->format('Y-m-d H:i');

        }
        return $last_call_tz_datetime;
    }

    public function getTimeZoneAppliedAttribute()
    {
        if (!empty($this->merchant) && !empty($this->merchant->time_zone)) {
            return $this->merchant->time_zone;
        }
        return 0;
    }

    function getFieldByMerchant($merchant_id)
    {
        return $this->newQuery()->with('merchant')->where('merchant_id', '=', $merchant_id)->firstOrFail();
    }
}