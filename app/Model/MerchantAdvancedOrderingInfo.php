<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class MerchantAdvancedOrderingInfo extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'Merchant_Advanced_Ordering_Info';
    protected $primaryKey = 'merchant_advanced_ordering_id';

    protected $fillable = ['merchant_id', 'max_days_out', 'catering_minimum_lead_time'];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

}
