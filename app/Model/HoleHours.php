<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class HoleHours extends Model
{
    protected $table = 'Hole_Hours';
    protected $fillable = [
        'id',
        'merchant_id',
        'day_of_week',
        'order_type',
        'start_time',
        'end_time',
        'logical_delete'
    ];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'merchant_id');
    }
}