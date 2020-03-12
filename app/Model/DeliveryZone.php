<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class DeliveryZone extends Model implements Auditable {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'Merchant_Delivery_Price_Distance';
    protected $primaryKey = 'map_id';
    protected $fillable = ['merchant_id'];

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}
