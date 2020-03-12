<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class MerchantDeliveryInfo extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'Merchant_Delivery_Info';
    protected $primaryKey = 'merchant_delivery_id';

    protected $fillable = ['merchant_id', 'minimum_order'];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}
