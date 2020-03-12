<?php

namespace App\Model;


use App\Model\Traits\BooleanHelper;
use Illuminate\Database\Eloquent\Model;

class MerchantCateringInfo extends Model
{
    use BooleanHelper;

    protected $table = 'Merchant_Catering_Infos';

    protected $fillable = [
        'active',
        'minimum_pickup_amount',
        'minimum_delivery_amount',
        'minimum_tip_percent',
        'lead_time_in_hours',
        'min_lead_time_in_hours_from_open_time',
        'maximum_number_of_catering_orders_per_day_part',
        'max_days_out',
        'accepted_payment_types',
        'allow_loyalty',
        'special_merchant_message_destination',
        'special_merchant_message_format',
        'catering_message_to_user_on_create_order',
        'delivery_active',
        'time_increment_in_minutes',
        'logical_delete'
    ];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'merchant_id');
    }

    public function getActiveAttribute($value)
    {
        return $this->castYNBoolean($value);
    }

    public function getDeliveryActiveAttribute($value)
    {
        return $this->castYNBoolean($value);
    }

    public function getLogicalDeleteAttribute($value)
    {
        return $this->castYNBoolean($value);
    }
}