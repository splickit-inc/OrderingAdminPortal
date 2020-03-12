<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class LoyaltyParkingLot extends Model
{
    protected $table = 'Loyalty_Parking_Lot_Records';
    protected $fillable = [
        'brand_id',
        'phone_number',
        'email',
        'remote_order_number',
        'amount',
        'location'
    ];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}