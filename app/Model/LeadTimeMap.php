<?php

namespace App\Model;

use function foo\func;
use Illuminate\Database\Eloquent\Model;

class LeadTimeMap extends Model {
    protected $table = 'Lead_Time_By_Day_Part_Maps';

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = ['merchant_id', 'hour_type', 'day_of_week', 'start_time', 'end_time', 'lead_time', 'logical_delete'];

    public function getLeadTimesByMerchant($merchant_id, $hour_type) {
        return static::where('merchant_id', '=', $merchant_id)->where('hour_type', '=', $hour_type);
    }

    /**
     * @param array $leadTimeData
     * @return bool
     */
    public function setLeadTime($leadTimeData) {
        if (array_key_exists('id', $leadTimeData)) {
            $leadTime = static::find($leadTimeData['id']);
        }
        else
        {
            $leadTime = new self();
        }
        $leadTime->fill($leadTimeData);
        $leadTime->save();
        return $leadTime;
    }

    /**
     * @param       $merchant_id
     * @param array $leadTimeData
     * @return mixed
     */
    public function validateLeadTime($merchant_id, $leadTimeData) {
        $result = static::where('merchant_id', '=', $merchant_id)
            ->where('hour_type', '=', $leadTimeData['hour_type'])
            ->where('day_of_week', '=', $leadTimeData['day_of_week'])
            ->where(function ($query) use ($leadTimeData) {
                $query->whereBetween('start_time', [$leadTimeData['start_time'], $leadTimeData['end_time']])
                    ->whereBetween('end_time', [$leadTimeData['start_time'], $leadTimeData['end_time']])
                    ->orWhere(function ($query) use ($leadTimeData) {
                        $query
                            ->where('start_time', '<', $leadTimeData['start_time'])->where('end_time', '>', $leadTimeData['start_time'])
                            ->orWhere('end_time', '>', $leadTimeData['end_time'])->where('start_time', '<', $leadTimeData['end_time']);
                    });
            });

        if (array_key_exists('id', $leadTimeData)) {
            $result = $result->where('id', '<>', $leadTimeData['id']);
        }

        if ($result->count() != 0) {
            return false;
        }
        return true;
    }

    public function deleteLeadTime($lead_time_id)
    {
        return static::find($lead_time_id)->delete();
    }
}
