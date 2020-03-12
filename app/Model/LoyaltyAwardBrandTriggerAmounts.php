<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class LoyaltyAwardBrandTriggerAmounts extends Model
{
    protected $table = 'Loyalty_Award_Brand_Trigger_Amounts';
    protected $primaryKey = 'id';

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'brand_id',
        'loyalty_award_trigger_type_id',
        'trigger_value',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'brand_id');
    }

    public function loyaltyMap()
    {
        return $this->hasMany(LoyaltyBrandBehaviorAwardAmountMap::class, 'loyalty_award_brand_trigger_amounts_id', 'id');
    }
}