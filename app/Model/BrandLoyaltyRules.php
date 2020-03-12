<?php

namespace App\Model;


use App\Model\Traits\BooleanHelper;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BrandLoyaltyRules extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use BooleanHelper;

    protected $table = 'Brand_Loyalty_Rules';
    protected $primaryKey = 'brand_loyalty_rules_id';

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'brand_id',
        'loyalty_type',
        'starting_point_value',
        'earn_value_amount_multiplier',
        'cliff_value',
        'cliff_award_dollar_value',
        'max_points_per_order',
        'loyalty_order_payment_type',
        'use_cheapest',
        'charge_modifiers_loyalty_purchase',
        'charge_tax',
        'logical_delete'
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'brand_id');
    }
}