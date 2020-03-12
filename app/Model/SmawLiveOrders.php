<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class SmawLiveOrders extends Model {

    protected $table = 'smawv_live_orders';
    protected $connection= 'reports_db';

    protected $primaryKey = null;

    //Timestamp Column Names in Smaw DB
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
}
