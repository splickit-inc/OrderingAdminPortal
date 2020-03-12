<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class UserDeliveryLocation extends Model implements Auditable  {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'User_Delivery_Location';
    protected $primaryKey = 'user_addr_id';

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

}
