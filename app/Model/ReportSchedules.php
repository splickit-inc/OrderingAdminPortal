<?php

namespace App\Model;


use App\BusinessModel\Reports\ScheduleFactory;
use Illuminate\Database\Eloquent\Model;

class ReportSchedules extends Model
{
    protected $table = 'portal_report_schedules';

    protected $primaryKey = 'id';

    protected $fillable = [
        'report_name',
        'frequency',
        'payload',
        'start_date',
        'end_date',
        'recipient',
        'available_at',
        'logical_delete'
    ];

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';


    public function addScheduleReport(array $data)
    {
        try {
            $data = array_intersect_key($data, array_flip($this->getFillable()));
            $data['available_at'] = ScheduleFactory::getScheduleNextDateByFrequency($data['frequency']);
            return $this->create($data);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function objectOwnership() {
        return $this->hasOne('App\Model\ObjectOwnership', 'id', 'object_id');
    }
}