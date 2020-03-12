<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;


class SmawvNb7DayOrders extends Model {

    protected $table = 'smawv_nb_7day_orders';
    protected $connection = 'reports_db';

    protected $primaryKey = 'order_id';

    public function merchant()
    {
        return $this->hasOne('App\Model\Merchant', 'merchant_id', 'merchant_id');
    }
}
