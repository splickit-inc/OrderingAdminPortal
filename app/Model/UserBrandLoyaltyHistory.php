<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class UserBrandLoyaltyHistory extends Model implements Auditable  {
    use \OwenIt\Auditing\Auditable;

    protected $table = 'User_Brand_Loyalty_History';

    public function brand() {
        return $this->belongsTo('App\Model\Brand', 'brand_id');
    }
}
