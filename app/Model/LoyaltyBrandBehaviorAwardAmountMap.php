<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class LoyaltyBrandBehaviorAwardAmountMap extends Model
{
    protected $table = 'Loyalty_Brand_Behavior_Award_Amount_Maps';
    protected $primaryKey = 'id';

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'brand_id',
        'loyalty_award_brand_trigger_amounts_id',
        'process_type',
        'value',
        'active',
        'first_date_available',
        'last_date_available',
        'history_label',
        'logical_delete'
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'brand_id');
    }

    public function awardTriggerAmount()
    {
        return $this->belongsTo(LoyaltyAwardBrandTriggerAmounts::class, 'loyalty_award_brand_trigger_amounts_id', 'id');
    }
}